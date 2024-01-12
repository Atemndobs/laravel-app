<?php

namespace App\Console\Commands\Song;

use App\Models\Catalog;
use App\Models\Song;
use App\Services\SongUpdateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use League\CommonMark\Extension\CommonMark\Parser\Block\ThematicBreakParser;
use function Amp\call;
use function example\ask;

class SongUpdateBpmCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:bpm {slug?} {--f|field=null} {--b|batch=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update song bpm and key , argument slug, options key and bpm';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $updateService = new SongUpdateService();
        $slug = $this->argument('slug');
        $bpm = $this->option('field') === 'bpm';
        $key = $this->option('field') === 'key';
        $batch = $this->option('batch');

        if ($slug !== null) {
            $song = Song::where('slug', '=', $slug)->first();
            if ($song === null) {
                $this->info('No song found for BPM Update. All song BPMs updated');
                return 0;
            }

            /** @var Song $song */
            if ($song->bpm !== null || $song->bpm !== 0) {
                $this->info('Song bpm already set');
                $res = $this->askWithCompletion('Do you want to update bpm?', ['y', 'n'], 'y');
                if ($res === 'n') {
                    $this->warn('Update skipped');
                    return 0;
                }

                $this->withProgressBar(1, function () use ($song, $bpm, $key, $updateService, &$updatedSongs) {
                $updatedSong = $this->getUpdatedSong($bpm, $key, $updateService, $song);
                $updatedSongs[] = $updatedSong;
                    $this->table(['slug', 'title', 'bpm', 'key', 'energy', 'scale'], [
                        $updatedSong,
                    ]);
            });

                return 0;
            }
            $this->info("prepare updating |  $song->slug");
            $updatedSong = $this->getUpdatedSong($bpm, $key, $updateService, $song);

            $this->table(['slug', 'title', 'bpm', 'key', 'energy', 'scale'], [
                $updatedSong,
            ]);

            return 0;
        }

        $songs = Song::query()->where('bpm', '<', 1)
            ->orWhereNull('bpm')
            ->get();
        $songCount = count($songs);
        $this->info($songCount.' songs found');
        if ($songCount === 0) {
            $this->info('No song found');
            return 0;
        }

        $info = [
            'songs' => [
                'ids' => $songs->pluck('id')->toArray(),
                'slugs' => $songs->pluck('slug')->toArray(),
                'titles' => $songs->pluck('title')->toArray(),
                'bpm' => $songs->pluck('bpm')->toArray(),
            ]
        ];
        $this->info(json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        dd(count($songs));

        foreach ($songs as $song) {
            // call the audio:fix command and pass the path as option using the song_url
            $this->call('audio:fix', ['--path' => $song->song_url]);
        }

        die('done');

        $updatedSongs = [];
        $this->output->progressStart($songCount);
        $this->newLine();
        // start time in seconds
        $start = time();
        /** @var Song $song */
        foreach ($songs as $position => $song) {
            if ((float)$song->bpm != 0) {
                $this->info('Song bpm already set | '. $song->bpm ."| ".$song->slug);
                Log::info('Song bpm already set | '. $song->bpm ."| ".$song->slug);
                $this->output->progressAdvance();
                continue;
            }

            $number = $position + 1;
            $left = $songCount - $position;
            // if the batch amt is reached exit
            if ($number > $batch) {
                $this->info("<fg=blue> Batch of $batch songs have been updated out of $songCount songs </>");
                $this->info("<fg=red;bg=cyan>$left songs left</>");
                Log::info("Batch of $batch songs have been updated out of $songCount songs");
                Log::warning("$left songs left");
                return 0;
            }
            $this->info("updating | $song->slug | $number song out of ".$songCount."| <fg=red;bg=cyan>$left songs left</>");
            Log::info("updating | $song->slug | $number song out of ".$songCount."| $left songs left");
            $updatedSong = $this->getUpdatedSong($bpm, $key, $updateService, $song);
            $this->table(['slug', 'title', 'bpm', 'key', 'energy', 'scale'], [
                $updatedSong,
            ]);
            Log::info("New BPM for $song->slug :" . $updatedSong['bpm']);
            $updatedSongs[] = $updatedSong;
            $this->output->progressAdvance();
            $this->newLine();
            // end time in seconds
            $end = time() - $start;
            // progress in seconds  - 1
            $this->warn("Time taken: ".($end)." seconds");

        }

        $this->output->progressFinish();
        $this->newLine();
        // table of updated songs
        $this->table(['slug', 'title', 'bpm', 'key', 'energy', 'scale'], $updatedSongs);

        // delete all queues in default queue
        $this->call('rabbitmq:queue-delete', ['name' => 'default']);
        $this->info("Updated songs: ".count($updatedSongs));

        $this->call('index:reindex');
        $this->call('scout:import', ['model' => Song::class]);
        $this->info('Scout index updated');
        $this->call('scout:import', ['model' => Catalog::class]);
        $this->info('Scout index updated');

        return 0;
    }

    /**
     * @param bool $bpm
     * @param bool $key
     * @param SongUpdateService $updateService
     * @param $song
     * @return array
     */
    public function getUpdatedSong(bool $bpm, bool $key, SongUpdateService $updateService, $song): array
    {
        $updatedSong = $song;
        if (! $bpm && ! $key) {
            $updatedSong = $updateService->updateBpmAndKey($song);
        }
        if ($bpm && ! $key) {
            $updatedSong = $updateService->updateBpm($song);
        }
        if ($key && ! $bpm) {
            $updatedSong = $updateService->updateKey($song);
        }

        return $updatedSong;
    }
}
