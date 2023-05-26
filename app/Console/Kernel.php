<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Spatie\Health\Commands\RunHealthChecksCommand;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $new_date = date('d M, Y', strtotime(now('CET')));
        $logFile = 'schedule_'.($new_date).'.log';

        $schedule->command("scout:import 'App\Models\Song'")
            ->daily()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo('storage/logs/downloads.log');

        $schedule->command("scout:index songs")
            ->daily()
            ->withoutOverlapping()
            ->runInBackground()
            ->description('Indexing songs')
            ->appendOutputTo('storage/logs/downloads.log');

        $schedule->command("song:bpm --batch 10")
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground()
            ->description('Update BPM for the next 10 songs')
            ->appendOutputTo('storage/logs/downloads.log');

        $schedule->command('watch:audio')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground()
            ->description('Watch Audio Dir for New Files')
            ->appendOutputTo('storage/logs/downloads.log');

        $schedule->command('watch:upload')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground()
            ->description('Moves upload Dir to audio and images')
            ->appendOutputTo('storage/logs/downloads.log');

        $schedule->command('rabbitmq:consume --queue=classify --max-jobs=2 --stop-when-empty')
            ->everyMinute()
            ->withoutOverlapping()
            ->description('Classify songs')
            ->appendOutputTo('storage/logs/classify.log');

        $schedule->command('rabbitmq:consume --queue=scout --stop-when-empty')
            ->everyMinute()
            ->withoutOverlapping()
            ->description('Indexing songs')
            ->appendOutputTo('storage/logs/indexer.log');

        $schedule->command(RunHealthChecksCommand::class)
            ->everyMinute();
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
