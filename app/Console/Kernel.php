<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\GenerateFiles::class,
        \App\Console\Commands\UpdateCurrencies::class,
        \App\Console\Commands\CalculateClassEarnings::class,
        \App\Console\Commands\CalculateInstructorStats::class,
        \App\Console\Commands\AlertsMonthly::class,
        \App\Console\Commands\AlertsDaily::class,
        \App\Console\Commands\NotificationsAndCancellations::class,
        \App\Console\Commands\DetermineTrends::class,
        \App\Console\Commands\DetermineTrends::class,
        \App\Console\Commands\AwsInstanceTurnOnOff::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('currencies:update')->hourly();
        $schedule->command('classes:trends')->dailyAt('01:00');
        $schedule->command('instructors:stats')->dailyAt('03:00');
        $schedule->command('classes:earnings')->dailyAt('05:00');
        $schedule->command('alerts:daily')->dailyAt('06:00'); // Needs to be after "classes:earnings" as it uses the calculated class ratings
        $schedule->command('alerts:monthly')->monthlyOn(2, '01:00');
        $schedule->command('triggers:notifyandcancel')->everyFiveMinutes(); // Do not change frequency, calculations rely on the 5 mins

        //Run cron command every minute to check if AWS instance can be turned off
        $schedule->command('aws:instance:turn-on-off ' . MINUTES_FOR_CLASS_TO_BEGIN)->everyMinute();
    }
}
