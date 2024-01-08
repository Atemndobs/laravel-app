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





        $songsWithoutAudio = [];

        if ($file !== null) {
            $songsWithoutAudio = file_get_contents($file);
            $songsWithoutAudio = explode("\n", $songsWithoutAudio);
            $songsWithoutAudio = array_filter($songsWithoutAudio);
            $songsWithoutAudio = array_unique($songsWithoutAudio);
            $songsWithoutAudioCount = count($songsWithoutAudio);
            $this->info("Found $songsWithoutAudioCount songs without audio");
            $bar = $this->output->createProgressBar($songsWithoutAudioCount);
            $bar->start();
            $processed = [];
            foreach ($songsWithoutAudio as $slug) {
                // left ?
                $this->info("Processing song with slug $slug");
                $songs = Song::query()->where('slug', $slug)->get();

                if ($songs->count() === 0) {
                    $this->warn("Song with slug $slug not found");
                    continue;
                }
                if ($songs->count() > 1) {
                    $this->warn("Song with slug $slug has more than one entry");

                    /** @var Song $song */
                    foreach ($songs as $song) {
                        $this->warn($song->id);
                        $songToDelete = null;
                        $songToKeep = null;
                        // the song to keep is the first song with song_id, song_url and source
                        if ($song->song_id !== null && $song->song_url !== null && $song->source !== null) {
                            $this->warn("Song to keep: {$song->id}");
                            $songToKeep = $song;
                        }else{
                            $this->warn("Song to delete: {$song->id}");
                            $songToDelete = $song;
                            $song->status = "duplicate";
                            $song->save();
                            // $song->delete();
                        }
//                        dump([
//                            'songToKeep' => [
//                                'id' => $songToKeep? $songToKeep->id : null,
//                                'song_id' => $songToKeep? $songToKeep->song_id : null,
//                                'song_url' => $songToKeep? $songToKeep->song_url : null,
//                                'source' => $songToKeep? $songToKeep->source : null,
//                                'status' => $songToKeep? $songToKeep->status : null,
//
//                            ],
//                            'songToDelete' => [
//                                'id' => $songToDelete? $songToDelete->id : null,
//                                'song_id' => $songToDelete? $songToDelete->song_id : null,
//                                'song_url' => $songToDelete? $songToDelete->song_url : null,
//                                'source' => $songToDelete? $songToDelete->source : null,
//                                'status' => $songToDelete? $songToDelete->status : null,
//                            ]
//                        ]);
                    }
                    continue;
                }
                /** @var Song $song */
                $song = $songs->first();
                $this->info("Processing song with slug $slug");
                $this->info("Song id: {$song->id}");
                $source = $song->source;
                $songUrl = $song->song_url;

                // if song url is null skip
                if ($songUrl === null) {
                    $this->warn("Song url is null");
                    continue;
                }
                if ($source === 'soundcloud') {

                    $this->info("Song source is soundcloud");
                    dump([
                        'url' => $songUrl,
                        'source' => 'spotify',
                        'id' => $song->id,
                        'slug' => $song->slug,
                        'path' => $song->path,
                       // 'song_id' => $song->toArray()
                    ]);
                    try {
                        $this->call('scrape:sc', [
                            '--link' => $songUrl,
                            '--continue' => true,
                        ]);
                    }catch (\Exception $e){
                        $this->error("SOUNDCLOUD ERROR: ");
                        $this->line("<fg=red>".json_encode($e->getMessage(),JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES )."</>");
                    }
                }

                if ($source === 'spotify') {
                    $this->info("Song source is spotify");
                    // call spotify command spotify $url
                    dump([
                        'url' => $songUrl,
                        'source' => 'spotify',
                        'id' => $song->id,
                        'slug' => $song->slug,
                        'path' => $song->path,
                     //   'song_id' => $song->toArray()
                    ]);
                    try {
                        $this->call('spotify', [
                            'url' => $songUrl,
                            '--force' => true,
                        ]);
                    }catch (\Exception $e){
                        $this->error("SPOTIFY ERROR: ");
                        $this->line("<fg=red>".json_encode($e->getMessage(),JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES )."</>");
                    }
                }
                $bar->advance();
                $processed[] = $slug;
                // write / add processed  slugs to file processed.txt
                $file = fopen("processed.txt", 'a');
                fwrite($file, $slug . "\n");
                fclose($file);
            }

            $message = [
                'processed' => count($processed),
                'total' => $songsWithoutAudioCount,
            ];
            $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $bar->finish();
        }

        return 0;
    }

}
