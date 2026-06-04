<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PaymentSchedule;
use Carbon\Carbon;

class AddDueInterestToLoans extends Command
{
    protected $signature   = 'schedule:add-due-interest {--date= : Test with a specific date (Y-m-d)}';
    protected $description = 'Add interest_amount to loans.interest when schedule due_date is today';

    public function handle(): void
    {
        $today = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : Carbon::today();

        $this->info("Running due interest addition for: {$today->toDateString()}");

        $schedules = PaymentSchedule::whereDate('due_date', $today->toDateString())
            ->where('closed', 0)
            ->get();

        if ($schedules->isEmpty()) {
            $this->info('No schedules due today.');
            return;
        }

        $processed = 0;
        $skipped   = 0;

        foreach ($schedules as $schedule) {
            $interestAmount = (float) $schedule->interest_amount;

            if ($interestAmount <= 0) {
                $this->info("Schedule {$schedule->schedule_code} has no interest, skipping.");
                $skipped++;
                continue;
            }

            $loan = DB::table('loans')
                ->where('loan_code', $schedule->loan_code)
                ->where('closed', 0)
                ->first();

            if (! $loan) {
                $this->warn("Loan not found or already closed for schedule: {$schedule->schedule_code}");
                $skipped++;
                continue;
            }

            DB::transaction(function () use ($schedule, $interestAmount) {
                DB::table('loans')
                    ->where('loan_code', $schedule->loan_code)
                    ->increment('interest', $interestAmount);

                DB::table('loans')
                    ->where('loan_code', $schedule->loan_code)
                    ->update([
                        'interest_balance' => DB::raw('interest - interest_paid'),
                    ]);
            });

            $this->info("Added interest {$interestAmount} to loan {$schedule->loan_code} from schedule {$schedule->schedule_code}");
            $processed++;
        }

        $this->info("Done. Processed: {$processed}, Skipped: {$skipped}");
        Log::info("Due Interest Addition — Processed: {$processed}, Skipped: {$skipped}, Date: {$today->toDateString()}");
    }
}