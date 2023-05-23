<?php

namespace App\Console\Commands\Scraper;

use Aerni\Spotify\SpotifyAuth;
use App\Services\Birdy\SpotifyService;
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
    protected $signature = 'spotify:import {playlist?}';

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
        $playlist = $this->argument('playlist');
        if ($playlist === null) {
            $playlist = 'https://open.spotify.com/playlist/6L395PhP6WoQIotqLYg7lQ?si=02eee911d5f046c8';
        }
        $likedSongs = shell_exec("spotdl $playlist");
        $songs = explode("\n", $likedSongs);
        Log::info(json_encode(['songs' => $songs],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}
