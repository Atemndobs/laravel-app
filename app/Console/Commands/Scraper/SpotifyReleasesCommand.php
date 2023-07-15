<?php

namespace App\Console\Commands\Scraper;

use App\Models\Release;
use Illuminate\Console\Command;
use App\Services\Scraper\SpotifyMusicService;

class SpotifyReleasesCommand extends Command
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

        if ($all) {
            $this->info('Getting all playlists...');
            $playlists = $spotifyService->getAllPlaylists();
            foreach ($playlists as $playlist) {
                $this->processPlaylist($playlist, $spotifyService);
            }
            $playlists = $spotifyService->preparePlaylistsTable($playlists);
            // remove image from table
            foreach ($playlists as $key => $playlist) {
                unset($playlists[$key]['image']);
            }
            $this->table(['id', 'name', 'owner', 'tracks', 'url'], $playlists);
            $this->info('Done!');

            return 0;
        }

        $playlist = $spotifyService->playlist($playlist);
        $this->processPlaylist($playlist, $spotifyService);

        $this->info('Done!');
        return 0;
    }

    /**
     * Process a playlist.
     *
     * @param array              $playlist
     * @param SpotifyMusicService $spotifyService
     */
    private function processPlaylist(array $playlist, SpotifyMusicService $spotifyService): void
    {
        $releases = new Release();

        $this->info('Checking if playlist exists in database...');
        $playlistExists = $spotifyService->playlistExists($playlist['id']);

        if ($playlistExists) {
            $this->warn('Playlist exists in database!');
            $tracksCount = $playlistExists->tracks;

            if ($tracksCount == $playlist['tracks']['total']) {
                $this->info('Number of tracks is the same!');
                $this->checkAndSavePlaylistSongs($playlist['id'], $spotifyService);

                return;
            }

            $this->info('Number of tracks is not the same!');
            $this->info('Updating playlist tracks count...');
            $playlistExists->tracks = $playlist['tracks']['total'];
            $playlistExists->save();
        } else {
            $playlistTable = $spotifyService->prepareSinglePlaylistTable($playlist);
            $spotifyService->savePlaylistInDB($playlistTable, $releases);
        }

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
        $playlistSongs = $spotifyService->prepareTracksTable($playlistSongs);
        $spotifyService->savePlaylistSongsInDB($playlistSongs);
        $this->table(['id', 'name', 'artist', 'album', 'url'], $playlistSongs);
    }

    /**
     * Check if playlist songs are the same and save them if necessary.
     *
     * @param string             $playlistId
     * @param SpotifyMusicService $spotifyService
     */
    private function checkAndSavePlaylistSongs(string $playlistId, SpotifyMusicService $spotifyService): void
    {
        $this->info('Checking if playlist songs are the same...');
        $playlistSongs = $spotifyService->getPlaylistSongs($playlistId);
        $playlistSongs = $spotifyService->prepareTracksTable($playlistSongs);
        $playlistSongsExists = $spotifyService->playlistSongsExists($playlistSongs);

        if ($playlistSongsExists) {
            $this->info('Playlist songs are the same!');
            return;
        }

        $this->info('Playlist songs are not the same!');
        $this->info('Getting latest playlist songs and saving them in the database');
        $spotifyService->savePlaylistSongsInDB($playlistSongs);
    }
}