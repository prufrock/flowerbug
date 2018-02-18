<?php

namespace App\Console;

use App\Console\Commands\AwsSimpleDbFlowerbugProjectsPut;
use App\Console\Commands\AwsSimpleDbGetItem;
use App\Console\Commands\AwsSimpleDbListDomainContents;
use App\Console\Commands\AwsSimpleDbListDomains;
use App\Console\Commands\S3Upload;
use App\Console\Commands\TestSimpleDB;
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
        AwsSimpleDbFlowerbugProjectsPut::class,
        AwsSimpleDbListDomains::class,
        AwsSimpleDbListDomainContents::class,
        AwsSimpleDbGetItem::class,
        S3Upload::class,
        TestSimpleDB::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
