<?php

namespace App\Console\Commands\Scraper;

use Illuminate\Console\Command;
use App\Services\Birdy\SpotifyService;
use App\Services\Scraper\SpotifyMusicService;

class SpotifyLikedSongsWatchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify:watch {--p|playlist=} {--a|all=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $playlist = $this->option('playlist');
        $all = $this->option('all');
        $spotifyService = new SpotifyMusicService();
        $this->info('Watching Spotify Liked Songs...');
        // if all is not null, get all playlists
        if ($all) {
            $playlists = $spotifyService->getAllPlaylists();
            $playlists = array_map(function ($playlist) {
                return [
                    'id' => $playlist['id'],
                    'name' => $playlist['name'],
                    'owner' => $playlist['owner']['display_name'],
                    'tracks' => $playlist['tracks']['total'],
                    'url' => $playlist['external_urls']['spotify'],
                ];
            }, $playlists);
            $this->table(['id', 'name', 'owner', 'tracks', 'url'], $playlists);
            $this->info('Done!');

            return 0;
        }
        $playlist = $spotifyService->playlist($playlist);
        // prepare playlist data
        $playlistData = [
            'id' => $playlist['id'],
            'name' => $playlist['name'],
            'owner' => $playlist['owner']['display_name'],
            'tracks' => $playlist['tracks']['total'],
            'url' => $playlist['external_urls']['spotify'],
        ];
        $this->table(['id', 'name', 'owner', 'tracks', 'url'], [$playlistData]);

        $this->info('Getting playlist songs...');
        $playlistSongs = $spotifyService->getPlaylistSongs($playlist['id']);
        $playlistSongs = array_map(function ($song) {
            return [
                'id' => $song['track']['id'],
                'name' => $song['track']['name'],
                'artist' => $song['track']['artists'][0]['name'],
                'album' => $song['track']['album']['name'],
                'url' => $song['track']['external_urls']['spotify'],
            ];
        }, $playlistSongs);
        $this->table(['id', 'name', 'artist', 'album', 'url'], $playlistSongs);


        $this->info('Done!');
    }
}
