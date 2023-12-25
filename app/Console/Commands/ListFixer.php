<?php

namespace App\Console\Commands;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListFixer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix {--p|path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Song links to point to aws s3 bucket';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // get all songs that are not on aws s3 bucket and update them. these songs have a path starting with http://s3.atemkeng.de:9000
        $allSongs = Song::query()->get();
        $this->info("Found {$allSongs->count()} songs in the database");
        $songs = Song::query()->where('path', 'like', 'http://s3.atemkeng.de:9000%')->get();
        // update the path to point to the aws s3 bucket in the format "https://s3.amazonaws.com/curators3/music/" +  $song->slug
        $count = $songs->count();
        $fixedSongs = [];
        $this->info("Found $count songs to fix");
        // progress bar start
        $bar = $this->output->createProgressBar(count($songs));
        $songs->each(function ($song) use (&$fixedSongs, $bar) {
            // if slug ens with mp3, remove it
            if (Str::endsWith($song->slug, 'mp3')) {
                $song->slug = Str::replaceLast('mp3', '', $song->slug);
            }
            $song->path = "https://s3.amazonaws.com/curators3/music/" . $song->slug . '.mp3';
            $song->save();
            $fixedSongs[] = $song->slug;
            $countFixed = count($fixedSongs);
            $this->info("Fixed $countFixed songs");
            $this->warn("Fixed {$song->slug}");
            $bar->advance();
            dd('PAUSE');
        });
        $bar->finish();
        $message = [
            'fixedSongs' => count($fixedSongs),
           // 'fixedSongsList' => $fixedSongs,
            'totalSongs' => $count,
        ];
        $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ));
        $this->info('==============Done================');
    }
}
