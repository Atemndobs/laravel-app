<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SongCleanUpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:cleanup {--e|exist=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $deletableSongs = Song::query()->whereNot('path', 'like', '%http%')->get();
        $totalSongs = Song::query()->count();
        $this->info("Total Songs: $totalSongs");
        if ($deletableSongs->count() <= 0) {
            $this->info('No songs to delete');
            return 0;
        }
        // progress bar start
        $bar = $this->output->createProgressBar(count($deletableSongs));
        $deletableSongs->each(function ($song) use ($bar) {
            $song->delete();
            $this->output->info("$song->title : deleted");
            $bar->advance();
        });
        $bar->finish();

        if ($this->option('exist')) {
            $this->info('Cleaning up songs that dont exist from the database');
            // progress bar start
            $bar = $this->output->createProgressBar(count($deletableSongs));
            $songs = Song::all();
            foreach ($songs as $song) {
                $url = str_replace('mage.tech:8899', 'nginx', $song->path);
                $filename = basename($url);
                $this->info("checking $filename");
                $fileExists = file_exists("/var/www/html/storage/app/public/audio/$filename");
                if (!$fileExists) {
                    $song->delete();
                    $this->output->info("$song->title : deleted");
                }

                $bar->advance();
            }

        }
        $bar->finish();
        return 0;
    }
}
