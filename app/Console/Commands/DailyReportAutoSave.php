<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DailyReport;
use App\Models\Company;
use App\Models\Loan;
use App\Models\Expense;
use Carbon\Carbon;
use DB;

class DailyReportAutoSave extends Command
{
    protected $signature = 'dailyreport:autosave';
    protected $description = 'Auto create daily report for all companies';

    public function handle()
    {
        $today = Carbon::now();
        $yesterday = Carbon::yesterday();

        $companies = Company::all();

        foreach ($companies as $company) {

            // Calculate loan_topup (sum of all loan_amount for this company created yesterday)
            $loan_topup = Loan::where('company_id', $company->id)
                ->whereDate('created_at', '<=', $yesterday)
                ->sum('loan_amount');

            // Calculate payment (sum of all payments for this company created yesterday)
            $payment = Loan::where('company_id', $company->id)
                ->whereDate('created_at', '<=', $yesterday)
                ->sum('payment');

            // Calculate expenses (sum of all expenses for this company created yesterday)
            $expenses = Expense::where('company_id', $company->id)
                ->whereDate('created_at', '<=', $yesterday)
                ->sum('amount');

            // Calculate account_total_amount (sum of all outstanding for this company)
            $account_total_amount = Loan::where('company_id', $company->id)
                ->sum('outstanding');

            DailyReport::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'closing_date' => $yesterday->format('Y-m-d'),
                ],
                [
                    'stock_a' => $company->stocka ?? 0,
                    'stock_b' => $company->stockb ?? 0,
                    'stock_bb' => $company->stockbb ?? 0,
                    'company_amount' => $company->amount ?? 0,
                    'loan_topup' => $loan_topup ?? 0,
                    'payment' => $payment ?? 0,
                    'expenses' => $expenses ?? 0,
                    'account_total_amount' => $account_total_amount ?? 0,
                    'created_date' => $today,
                ]
            );
        }

        $this->info('Daily Reports Auto Created');
        return 0;
    }
}