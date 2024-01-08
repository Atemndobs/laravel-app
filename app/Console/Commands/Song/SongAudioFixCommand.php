<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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


        $songs = Song::query()->whereNot('path', 'like', 'https://%')->get();
          $songsCount = $songs->count();
            $this->info("Found $songsCount songs to fix");
            dump([
               // 'PATHS TO FIX' => $songs->pluck('path')->toArray(),
                'songsCount' => $songsCount,
            ]);
            // for each of the songs, get their slug and  find all sungs with the same slug
            $bar = $this->output->createProgressBar(count($songs));
            $bar->start();
            $this->line("");
            foreach ($songs as $song) {
                $slug = $song->slug;
                $songsWithSameSlug = Song::query()->where('slug', '=', $slug)->get();
                $songsWithSameSlugCount = $songsWithSameSlug->count();
                $this->warn("SONG ID {$song->id} | Found $songsWithSameSlugCount songs with the same slug");
                $this->info("Found $songsWithSameSlugCount songs with the same slug");
                // for each of the songs with the same slug, update the path to point to the new storage
                /** @var Song $songWithSameSlug */
                foreach ($songsWithSameSlug as $songWithSameSlug) {
                    dump([
                        'slug' => $songWithSameSlug->slug,
                        'path' => $songWithSameSlug->path,
                        'song_id' => $songWithSameSlug->song_id,
                        'song_url' => $songWithSameSlug->song_url,
                        'source' => $songWithSameSlug->source,
                    ]);
                  //  if the song path starts with https, its the song to keep., If the path starts with /var/www/html/storage its the song to delete
                    if (Str::startsWith($songWithSameSlug->path, 'https://')) {
                        $songToKeep = $songWithSameSlug;
                    }
                    if (Str::startsWith($songWithSameSlug->path, '/var/www/html/storage')) {
                        $songToDelete = $songWithSameSlug;
                    }

                    // Update the song to keep with song_id, song_url, source, from song to delete and delete the song to delete
                    if (isset($songToKeep) && isset($songToDelete)) {
                        $songToKeep->song_id = $songToDelete->song_id;
                        $songToKeep->song_url = $songToDelete->song_url;
                        $songToKeep->source = $songToDelete->source;
                        $songToKeep->save();
                      // $songToDelete->forceDelete();
                        $this->info("Updated song to keep with song_id, song_url, source, from song to delete and deleted the song to delete");
                        dump([
                            'id' => $songToKeep->id,
                            'slug' => $songToKeep->slug,
                            'path' => $songToKeep->path,
                            'song_id' => $songToKeep->song_id,
                            'song_url' => $songToKeep->song_url,
                            'source' => $songToKeep->source,
                        ]);
                    }
                }
                $bar->advance();
              //  dd("STOP_1");
            }



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

            // find the missing songs and update the song
            // Get the song from the database and update the song


            return 0;
        }


        dd("STOP");



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
