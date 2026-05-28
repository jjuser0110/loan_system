<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PaymentSchedule;
use Carbon\Carbon;

class RolloverSkimASchedule extends Command
{
    protected $signature   = 'schedule:rollover-skim-a';
    protected $description = 'Auto-create next month payment schedule for SKIM A loans whose latest due_date is today';

    public function handle(): void
    {
        $today = Carbon::today();

        $this->info("Running SKIM A rollover for due_date: {$today->toDateString()}");

        $loans = DB::table('loans')
            ->where('interest_group', 'SKIM A')
            ->where('closed', 0)
            ->whereColumn('paid', '<', 'payment')
            ->select('loan_code', 'company_id', 'customer_id')
            ->get();

        if ($loans->isEmpty()) {
            $this->info('No SKIM A loans found.');
            return;
        }

        $created = 0;
        $skipped = 0;

        foreach ($loans as $loan) {
            $latestSchedule = PaymentSchedule::where('loan_code', $loan->loan_code)
                ->orderByDesc('due_date')
                ->first();

            if (! $latestSchedule) {
                $this->warn("No schedule found for loan_code: {$loan->loan_code}");
                continue;
            }

            $latestDueDate = Carbon::parse($latestSchedule->due_date);

            // Only rollover if latest due_date is today or in the past
            if ($latestDueDate->greaterThan($today)) {
                $this->info("Loan {$loan->loan_code} already up to date (latest: {$latestDueDate->toDateString()}), skipping.");
                $skipped++;
                continue;
            }

            $currentSchedule    = $latestSchedule;
            $totalInterestAdded = 0;

            while (true) {
                $nextDueDate = Carbon::parse($currentSchedule->due_date)->addMonth();

                $alreadyExists = PaymentSchedule::where('loan_code', $loan->loan_code)
                    ->whereDate('due_date', $nextDueDate->toDateString())
                    ->first();

                if ($alreadyExists) {
                    // Move pointer forward, don't double-count interest
                    $currentSchedule = $alreadyExists;
                } else {
                    $newScheduleCode = $this->incrementScheduleCode($currentSchedule->schedule_code, $loan->loan_code);

                    $currentSchedule = PaymentSchedule::create([
                        'schedule_code'        => $newScheduleCode,
                        'loan_code'            => $currentSchedule->loan_code,
                        'company_id'           => $currentSchedule->company_id,
                        'customer_id'          => $currentSchedule->customer_id,
                        'due_date'             => $nextDueDate->toDateString(),
                        'payment_amount'       => 0,
                        'paid_amount'          => 0,
                        'discount_amount'      => 0,
                        'interest_amount'      => $currentSchedule->interest_amount,
                        'interest_paid_amount' => 0,
                        'late_amount'          => 0,
                        'late_paid_amount'     => 0,
                        'closed'               => 0,
                    ]);

                    $totalInterestAdded += (float) $currentSchedule->interest_amount;

                    $this->info("Created {$currentSchedule->schedule_code} for {$loan->loan_code} due {$nextDueDate->toDateString()}");
                    $created++;
                }

                // Stop once we have a schedule beyond today
                if ($nextDueDate->greaterThan($today)) {
                    break;
                }
            }

            // Update interest in loans for all newly created schedules
            if ($totalInterestAdded > 0) {
                DB::table('loans')
                    ->where('loan_code', $loan->loan_code)
                    ->increment('interest', $totalInterestAdded);

                $this->info("Updated interest +{$totalInterestAdded} for {$loan->loan_code}");
            }
        }

        $this->info("Done. Created: {$created}, Skipped: {$skipped}");
        Log::info("SKIM A Rollover — Created: {$created}, Skipped: {$skipped}, Date: {$today->toDateString()}");
    }

    private function incrementScheduleCode(string $code, string $loanCode): string
    {

        $suffix = substr($code, strlen($loanCode));

        $suffix = preg_replace('/-\d+$/', '', $suffix);

        if (preg_match('/^(.*?)(\d+)$/', $suffix, $matches)) {
            $suffixPrefix = $matches[1]; // e.g. -SM or -S
            $number       = (int) $matches[2];
            $padded       = str_pad($number + 1, strlen($matches[2]), '0', STR_PAD_LEFT);
            return $loanCode . $suffixPrefix . $padded;
        }

        return $loanCode . '-S001';
    }
}