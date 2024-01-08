<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Http;
use const Widmogrod\Monad\Writer\log;

class SongAudioFixCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audio:fix  {--p|path=} {--a|all} {--d|dry-run} {--b|batch=} {--f|file=} {--s|skip=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix Song links to point to aws s3 bucket and check if they are working using batch requests';


    /**s
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $all = $this->option('all');
        $path = $this->option('path');
        $dryRun = $this->option('dry-run');
        $batch = $this->option('batch');
        $file = $this->option('file');
        $skip = $this->option('skip');
        // if path is not provided, create a folder in root directory called fixed
        if ($path === null) {
            $path = 'fixed';
        }
        if ($batch === null) {
            $batch = 100;
        }
//        if ($file === null) {
//            $file = 'songsWithAudio.txt';
//        }
//        if ($skip === null) {
//            $skip = 'songsWithoutAudio.txt';
//        }

        // If file is given then read the file and get the songs from the file using the slug. All these songs should have audio therefore they should be skipped

        $songs = Song::query()->get();
        if ($file !== null) {
            $songsWithAudio = file_get_contents($file);
            $songsWithAudio = explode("\n", $songsWithAudio);
            $songsWithAudio = array_filter($songsWithAudio);
            $songsWithAudio = array_unique($songsWithAudio);
            $songsWithAudioCount = count($songsWithAudio);
            $this->info("Found $songsWithAudioCount songs with audio");
            $songs = Song::query()->whereNotIn('slug', $songsWithAudio)->get();
            $songsCount = $songs->count();
            $this->info("Found $songsCount songs to fix");
           // return 0;
        }



        // if skip is given then read the file and get the songs from the file using the slug
        if ($skip !== null) {
            $songsWithoutAudio = file_get_contents($skip);
            $songsWithoutAudio = explode("\n", $songsWithoutAudio);
            $songsWithoutAudio = array_filter($songsWithoutAudio);
            $songsWithoutAudio = array_map('trim', $songsWithoutAudio);
            $songsWithoutAudio = array_unique($songsWithoutAudio);
            $songsWithoutAudioCount = count($songsWithoutAudio);
            $this->info("Found $songsWithoutAudioCount songs without audio");
            $songs = Song::query()->whereIn('slug', $songsWithoutAudio)->get();
            $songsCount = $songs->count();
            $this->info("Found $songsCount songs to fix");
            return 0;
        }




        // log out the full path
        $fullPath = base_path($path);
        $this->info("Full path: $fullPath");
        $songsWithAudio = [];
        $songsWithoutAudio = [];
        // get all songs
        $totalSongs = Song::query()->get();
        $totalSongsCount = Song::query()->count();
        $songsCount = count($songs);

        $info = [
            'totalSongsCount' => $songsCount,
            'songsCount' => $songsCount,
            'batch' => $batch,
            'path' => $path,
        ];
        $this->warn(json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        // for all songs, check if the path url is working in batches of 100

        $bar = $this->output->createProgressBar(count($songs));
        $bar->start();
        $this->line("");
        foreach ($songs as $song) {
            $bar->advance();
            $this->line("");
            $url = $song->path;
            $this->info("Checking $url");
            $response = Http::get($url);
            if ($response->successful()) {
                $this->info("Song $url is working");
                $songsWithAudio[] = $song;
                // write or add the song to a file named songsWithAudio.txt
                $file = fopen("songsWithAudio.txt", 'a');
                fwrite($file, $song->slug . "\n");
                fclose($file);

            } else {
                $this->line("<fg=red> $url is not working</>");
                $songsWithoutAudio[] = $song;
                // write or add the song to a file named songsWithoutAudio.txt
                $file = fopen("songsWithoutAudio.txt", 'a');
                fwrite($file, $song->slug . "\n");
                fclose($file);
            }
            // output songs left to check
            $songsLeft = $songsCount - count($songsWithAudio);
            $this->line("<fg=yellow> $songsLeft songs left to check</>");
            if ($songsLeft === 0) {
                $this->info("All songs have been checked");
                break;
            }
            
        }
        $bar->finish();
        $this->line("");
        // log out the number of songs with audio and without audio
        $songsWithAudioCount = count($songsWithAudio);
        $songsWithoutAudioCount = $songsCount - $songsWithAudioCount;
        $stats = [
            'songsCount' => $songsCount,
            'songsWithAudioCount' => $songsWithAudioCount,
            'songsWithoutAudioCount' => $songsWithoutAudioCount,
        ];
        $this->info(json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return 0;
    }
}
