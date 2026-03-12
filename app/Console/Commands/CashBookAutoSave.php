<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CashBookReport;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodLog;
use Carbon\Carbon;

class CashBookAutoSave extends Command
{
    protected $signature = 'cashbook:autosave';
    protected $description = 'Auto save cash book report daily';

    public function handle()
    {
        $yesterday = Carbon::yesterday();
        
        $this->info("Looking for all payment method logs");
        
        $paymentMethodLogs = PaymentMethodLog::all(); // Get all logs
        
        $this->info("Found " . $paymentMethodLogs->count() . " payment method logs");

        foreach ($paymentMethodLogs as $log) {
            $this->info("Processing log ID: " . $log->id);
            
            $paymentMethod = PaymentMethod::find($log->payment_method_id);

            if (!$paymentMethod) {
                $this->warn("Payment method not found for log ID: " . $log->id);
                continue;
            }

            $this->info("Payment method found. Company ID: " . $paymentMethod->company_id);

            // Build description
            $description = '-';

            $type = $log->type
                ? strtolower(class_basename($log->type))
                : 'manual';

            if ($type === 'manual') {
                $description = "Manual # " . ($log->description ?? '-');
            } elseif ($log->content) {
                $description = match ($type) {
                    'payment' => $log->content->payment_code
                                    ? "Payment #{$log->content->payment_code}"
                                    : "Payment # " . ($log->description ?? '-'),
                    'loan'    => $log->content->loan_code
                                    ? "Loan #{$log->content->loan_code}"
                                    : "Loan # " . ($log->description ?? '-'),
                    'expense' => $log->content->expense_code
                                    ? "Expense #{$log->content->expense_code}"
                                    : "Expense # " . ($log->description ?? '-'),
                    default   => '-',
                };
            } else {
                // content is null — related record was deleted
                $description = match ($type) {
                    'payment' => "Payment # " . ($log->description ?? '-'),
                    'loan'    => "Loan # " . ($log->description ?? '-'),
                    'expense' => "Expense # " . ($log->description ?? '-'),
                    default   => '-',
                };
            }

            $customerName = null;
            $expenseName  = null;

            if (in_array($type, ['payment', 'loan']) && $log->content) {
                $customerName = $log->content->customer?->customer_name ?? null;
            }

            if ($type === 'expense' && $log->content) {
                $expenseName = $log->content->expense_title ?? null;
            }

            $amount    = $log->amount ?? 0;
            $loanTopUp = 0;
            $payment   = 0;
            $expense   = 0;
            $manual    = 0;

            if ($type === 'loan') {
                $loanTopUp = $amount;
            } elseif ($type === 'payment') {
                $payment = $amount;
            } elseif ($type === 'expense') {
                $expense = $amount;
            } elseif ($type === 'manual') {
                $manual = $amount;
            }

            $report = CashBookReport::updateOrCreate(
                [
                    'payment_method_log_id' => $log->id,
                ],
                [
                    'company_id'           => $paymentMethod->company_id,
                    'date'                 => Carbon::parse($log->created_at)->format('Y-m-d'),
                    'description'          => $description,
                    'account_total_amount' => $log->total ?? 0,
                    'payment'              => $payment,
                    'loan_top_up'          => $loanTopUp,
                    'expenses'             => $expense,
                    'customer_name'        => $customerName,
                    'expenses_name'        => $expenseName,
                ]
            );

            $this->info("Created report ID: " . $report->id);
        }

        $this->info('Cash Book Auto Saved');
        return 0;
    }
}