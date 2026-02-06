<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DailyReport;
use App\Models\Company;
use Carbon\Carbon;

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

            // prevent duplicate same day
            DailyReport::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'closing_date' => $yesterday,
                ],
                [
                    'stock_a' => $company->stocka,
                    'stock_b' => $company->stockb,
                    'stock_bb' => $company->stockbb,
                    'company_amount' => $company->amount,
                    // 'loan_topup' => 0,
                    // 'payment' => 0,
                    // 'expenses' => 0,
                    // 'account_total_amount' => 0,
                    'created_date' => $today,
                ]
            );
        }

        $this->info('Daily Reports Auto Created');
    }
}