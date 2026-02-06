<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CashBookReport;
use App\Models\Loan;
use Carbon\Carbon;

class CashBookAutoSave extends Command
{
    protected $signature = 'cashbook:autosave';
    protected $description = 'Auto save cash book report daily';

    public function handle()
    {
        $loans = Loan::all();

        foreach ($loans as $loan) {
            CashBookReport::create([
                'company_id' => $loan->company_id,
                'date' => Carbon::today(),
                'description' => 'Auto Generated',
                'loan_top_up' => $loan->loan_amount,
                'payment' => $loan->payment,
                'expenses' => $loan->company_id,
                'account_total_amount' => $loan->company_id,
            ]);
        }

        $this->info('Cash Book Auto Saved');
    }
}