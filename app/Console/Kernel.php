<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    
    protected $commands = [
        //
	   'App\Console\Commands\DoClosings',
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('daily:cron')->daily();
        $schedule->command('dailyreport:autosave')->dailyAt('00:00');
        $schedule->command('cashbook:autosave')->dailyAt('00:00');
        $schedule->command('schedule:rollover-skim-a')
                ->dailyAt('00:00')          // runs every day just after midnight
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/skim-a-rollover.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
