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
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $companies = Company::all();

        foreach ($companies as $company) {

            // Calculate loan_topup (sum of all loan_amount for this company created yesterday)
            $loan_topup = Loan::where('company_id', $company->id)
                ->sum('loan_amount');

            // Calculate payment (sum of all payments for this company created yesterday)
            $payment = Loan::where('company_id', $company->id)
                ->sum('payment');

            // Calculate account_total_amount (sum of all outstanding for this company)
            $account_total_amount = Loan::where('company_id', $company->id)
                ->sum('outstanding');

            // Calculate expenses (sum of all expenses for this company created yesterday)
            $expenses = Expense::where('company_id', $company->id)
                ->sum('amount');

            // Prevent duplicate same day
            DailyReport::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'closing_date' => $yesterday->format('Y-m-d'),
                ],
                [
                    'stock_a' => $company->stocka,
                    'stock_b' => $company->stockb,
                    'stock_bb' => $company->stockbb,
                    'company_amount' => $company->amount,
                    'loan_topup' => $loan_topup,
                    'payment' => $payment,
                    'expenses' => $expenses,
                    'account_total_amount' => $account_total_amount,
                    'created_date' => $today,
                ]
            );
        }

        $this->info('Daily Reports Auto Created');
    }
}