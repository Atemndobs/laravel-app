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
    protected $signature = 'audio:fix  {--p|path=} {--a|all} {--d|dry-run} {--b|batch=} {--f|file=}';

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
        // if path is not provided, create a folder in root directory called fixed
        if ($path === null) {
            $path = 'fixed';
        }

        // log out the full path
        $fullPath = base_path($path);
        $this->info("Full path: $fullPath");
        $songsWithAudio = [];
        $songsWithoutAudio = [];
        // get all songs
        $songs = Song::query()->get();
        $songsCount = Song::query()->count();

        $info = [
            'songsCount' => $songsCount,
            'batch' => $batch,
            'path' => $path,
        ];
        $this->warn(json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        // for all songs, check if the path url is working in batches of 100


        foreach ($songs as $song) {
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
                $this->info("Song $url is not working");
                $songsWithoutAudio[] = $song;
                // write or add the song to a file named songsWithoutAudio.txt
                $file = fopen("songsWithoutAudio.txt", 'a');
                fwrite($file, $song->slug . "\n");
                fclose($file);
            }
        }
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
