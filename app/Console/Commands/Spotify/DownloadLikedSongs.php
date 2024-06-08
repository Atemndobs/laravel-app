<?php

namespace App\Console\Commands\Spotify;

use App\Services\Scraper\SpotifyMusicService;
use Illuminate\Console\Command;
use const example\int;

class DownloadLikedSongs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:liked-songs {--t|time=} {--p|playlist=} {--a|all=} {--l|limit=} {--r|release-radar=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download Liked Songs from Spotify from the last 24 hours options are 
    --time (in hours; default 24), 
    --playlist [-p playlist name], 
    --all [-a ] (download all liked songs), 
    --limit [-l] (limit the number of songs to download),
    --release-radar [-r] (download release radar playlist)
    ';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        // get playlist Id from Liked Songs
        $time = $this->option('time') ?? 24;
        $limit = $this->option('limit') ?? 50;
        $releaseRadar = $this->option('release-radar');
        if (!$releaseRadar) {
            $releaseRadar = 'ATM Release Radar';
        }
        if (str_contains($time, 'd')) {
            $time = intval($time) * 24;
        }
        $spotifyService = new SpotifyMusicService();

        $likedSongs = $spotifyService->getLikedSongsIds($time, $limit);
        $countLikedSongs = count($likedSongs);


        // if hours > 24 convert to days
        if ($time > 24) {
            $time = $time / 24;
            $time = $time . ' days';
        } else {
            $time = $time . ' hours';
        }
        $this->info('Found : ' . $countLikedSongs . ' songs since ' . $time . ' ago.');
        $downloadables = [];
        foreach ($likedSongs as $likedSong) {
            // check if ID exists in DB
            $songExists = $spotifyService->checkIfSongExists($likedSong);
            if ($songExists) {
                $this->error('Song with ID ' . $likedSong['id'] . ' already exists in DB.');
            } else {
                $this->warn('Song with ID ' . $likedSong['id'] . ' does not exist in DB. Adding...');
                $this->downloadSongBySpotifyId($likedSong['share_url']);
                $downloadables[] = $likedSong['share_url'];
            }
        }
        $this->info('Downloaded ' . count($downloadables) . ' songs.');
    }

    public function downloadSongBySpotifyId(string $url)
    {
        $this->call('spotify', [
            'url' => $url,
        ]);
    }
}
