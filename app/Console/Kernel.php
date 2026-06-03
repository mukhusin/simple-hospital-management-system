<?php

namespace App\Console;

use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
  
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
       try {
              $schedule->command('nhif-claims')->weeklyOn(1, '01:00');
       } catch (Exception $e) {
           Log::channel('system_logs')->error("schedule:generate ".$e);
       }
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


// * * * * * cd /home/mukhusin/dev/hrms && php artisan schedule:run >> /dev/null 2>&1