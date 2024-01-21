<?php

namespace App\Console\Commands\Scraper;

use Aerni\Spotify\SpotifyAuth;
use App\Services\Birdy\SpotifyService;
use App\Services\Scraper\SpotifyMusicService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use function example\ask;

class SpotifyLikedSongsImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:import {playlist?} {--o|offset=} {--l|limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Spotify Liked Songs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $offset = $this->option('offset');
        $limit = $this->option('limit');
        $playlist = $this->argument('playlist');
        Log::warning('Importing Spotify playlist: ' . $playlist );
        $this->info('Importing Spotify playlist: ' . $playlist );
        if ($playlist === null) {
            $this->info('No playlist provided, using default playlist : Liked Songs');
            Log::info('No playlist provided, using default playlist : Liked Songs');
            $playlist = 'https://open.spotify.com/playlist/6L395PhP6WoQIotqLYg7lQ?si=02eee911d5f046c8';
        }
        $spotifyService = new SpotifyMusicService();
        try {
            $playlistData = $spotifyService->getSpotifyIdsFromPlaylist($playlist, $offset, $limit);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->error($e->getMessage());
            $this->line("<fg=bright-magenta>Please provide a valid Spotify playlist URL</>");
            return 0;
        }

        $this->info('Importing ' . count($playlistData) . ' songs from Spotify playlist: ' . $playlist );
        $spotifyIds = $playlistData['spotifyIds'];
        $url = $playlistData['url'];
        $spotifyIngo = [
            'url ' => $url,
            'total_songs' => $playlistData['total_songs'],
            'batch_size' => $limit,
            'skipped_songs' => $playlistData['skipped_songs'],
            'download_batch' => count($spotifyIds),
            ];
        Log::info(json_encode($spotifyIngo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $this->info(json_encode($spotifyIngo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $songs = [];

        $this->warn("Found Spotify IDs: " . count($spotifyIds));
        // Start progress bar
        $bar = $this->output->createProgressBar(count($spotifyIds));
        // start timer
        $startTime = microtime(true);
        $originalEstimatedTime = count($spotifyIds) * 30 / 60 . " mins";
        foreach ($spotifyIds as $spotifyId) {
            $this->line('');
            $bar->advance();
            $this->line('');
            $songUrl = 'https://open.spotify.com/track/' . $spotifyId;
            try {
                $this->call('spotify', [
                    'url' => $songUrl
                ]);
            }catch (\Exception $e) {
                Log::error($e->getMessage());
                $this->error($e->getMessage());
                $this->line("<fg=bright-magenta>We shall retry downloading $spotifyId after 20 seconds</>");
                // elapsed time in mins
                $elapsedTime = (microtime(true) - $startTime) / 60 . " mins";
                $stimatedTime = (count($spotifyIds) - count($songs)) * 20 / 60 . " mins";
                $this->warn("Elapsed time: " . $elapsedTime );
                $this->warn("Estimated time left: " . $stimatedTime );
                Log::info("We shall retry downloading $spotifyId after 20 seconds");
                sleep(20);
                Log::info("Retrying $spotifyId");
                $this->line('');
                $this->line("<fg=magenta>Retrying $spotifyId</>");
                $this->call('spotify', [
                    'url' => $songUrl
                ]);
                continue;
            }
            $songs[] = $spotifyId;
            $this->info('Song with ID ' . $spotifyId . ' has successfully downloaded.');
            $elapsedTime = (microtime(true) - $startTime) / 60 . " mins";
            $estimatedTime = (count($spotifyIds) - count($songs)) * 20 / 60 . " mins";
            $spotifyInfo = [
                'downloaded_songs' => count($songs),
                'songs_left' => count($spotifyIds) - count($songs),
                'elapsed_time' => $elapsedTime,
                'estimated_time_left' => $estimatedTime,
            ];
            $this->line("<fg=bright-cyan>Downloaded songs: " . json_encode($spotifyInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</>");
            Log::warning(json_encode($spotifyInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
        $this->line('');
        $bar->finish();

        $elapsedTime = (microtime(true) - $startTime) / 60 . " mins";
        $completeInfo = [
            'stats' => 'Completed',
            'downloaded_songs' => count($songs),
            'elapsed_time' => $elapsedTime,
            'originally_estimated_time' => $originalEstimatedTime
        ];
        Log::info(json_encode($completeInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $this->line("<fg=bright-white> ================ Download Successfully completed Completed ============================= </>");
        $this->line("<fg=bright-cyan>". json_encode($completeInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)." </>");
        $this->call('move:audio');
        $this->call('s3:multi-put', [
            '--source' => 'audio',
            '--directory' => 'music',
        ]);
        $this->call('song:import');
        return 0;
    }
}