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

            if ($log->content_type && $log->content) {
                $type = class_basename($log->content_type);

                $description = match (strtolower($type)) {
                    'payment' => "Payment #{$log->content->payment_code}",
                    'loan'    => "Loan #{$log->content->loan_code}",
                    'expense' => "Expense #{$log->content->id}",
                    default   => '-',
                };
            }

            $amount = abs($log->amount ?? 0);
            $loanTopUp = 0;
            $payment   = 0;
            $expense   = 0;

            if ($log->content) {
                $type = strtolower(class_basename($log->content_type));

                if ($type === 'loan') {
                    $loanTopUp = $amount;
                }

                if ($type === 'payment') {
                    $payment = $amount;
                }

                if ($type === 'expense') {
                    $expense = $amount;
                }
            }

            $report = CashBookReport::create([
                'company_id' => $paymentMethod->company_id,
                'date' => Carbon::parse($log->created_at)->format('Y-m-d'), // Use log's creation date
                'description' => $description,
                'account_total_amount' => $log->total ?? 0,
                'payment' => $payment,
                'loan_top_up' => $loanTopUp,
                'expenses' => $expense,
            ]);

            $this->info("Created report ID: " . $report->id);
        }

        $this->info('Cash Book Auto Saved');
        return 0;
    }
}