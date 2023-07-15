<?php

namespace App\Console\Commands\Scraper;

use App\Models\Release;
use Illuminate\Console\Command;
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
            $this->info('Getting all playlists...');
            $playlists = $spotifyService->getAllPlaylists();
            $playlists = $spotifyService->preparePlaylistsTable($playlists);

            foreach ($playlists as $playlist) {
                $releases = new Release();
                // check if playlist exists in database and if number of tracks is the same
                $this->info('Checking if playlist exists in database...');
                $playlistExists = $spotifyService->playlistExists($playlist['id']);
                if ($playlistExists) {
                    $this->warn('Playlist exists in database!');
                    $this->info('Checking if number of tracks is the same...');
                    $tracksCount = $playlistExists->tracks;
                    if ($tracksCount == $playlist['tracks']) {
                        $this->info('Number of tracks is the same!');
                        continue;
                    }
                    $this->info('Number of tracks is not the same!');
                    // update playlist tracks count
                    $this->info('Updating playlist tracks count...');
                    $playlistExists->tracks = $playlist['tracks'];
                    $playlistExists->save();
                    // get latest | newest playlist songs and save them in database
                    $this->info('Getting latest playlist songs...');
                    $playlistSongs = $spotifyService->getPlaylistSongs($playlist['id']);
                    $playlistSongs = $spotifyService->prepareTracksTable($playlistSongs);
                    $spotifyService->savePlaylistSongsInDB($playlistSongs);
                    // @todo: download latest | newest playlist songs
                    $playlistSongs = $spotifyService->getPlaylistSongs($playlist['id']);
                    $playlistSongs = $spotifyService->prepareTracksTable($playlistSongs);
                    $spotifyService->savePlaylistSongsInDB($playlistSongs);
                }
                $spotifyService->savePlaylistInDB($playlist, $releases);
                // check if number of tracks from playlist is the same as in database
                // if not, get playlist songs

                $this->info('Getting playlist songs...');
                $playlistSongs = $spotifyService->getPlaylistSongs($playlist['id']);
                $playlistSongs = $spotifyService->prepareTracksTable($playlistSongs);
                $spotifyService->savePlaylistSongsInDB($playlistSongs);
            }
            $this->table(['id', 'name', 'owner', 'tracks', 'url'], $playlists);
            $this->info('Done!');

            return 0;
        }
        $playlist = $spotifyService->playlist($playlist);
        // check if playlist exists in database and if number of tracks is the same
        $this->info('Checking if playlist exists in database...');
        $playlistExists = $spotifyService->playlistExists($playlist['id']);
        if ($playlistExists) {
            $this->warn('Playlist exists in database!');
            $this->info('Checking if number of tracks is the same...');
            $tracksCount = $playlistExists->tracks;
            if ($tracksCount == $playlist['tracks']['total']) {
                $this->info('Number of tracks is the same!');

                return 0;
            }
            $this->info('Number of tracks is not the same!');
            // update playlist tracks count
            $this->info('Updating playlist tracks count...');
            $playlistExists->tracks = $playlist['tracks']['total'];
            $playlistExists->save();

            // get latest | newest playlist songs and save them in database
            $this->info('Getting latest playlist songs...');
            $playlistSongs = $spotifyService->getPlaylistSongs($playlist['id']);
            $playlistSongs = $spotifyService->prepareTracksTable($playlistSongs);
            $spotifyService->savePlaylistSongsInDB($playlistSongs);
            return 0;
        }
        $releases = new Release();
        $spotifyService->savePlaylistInDB($playlist, $releases);

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
        $playlistSongs = $spotifyService->prepareTracksTable($playlistSongs);
        $spotifyService->savePlaylistSongsInDB($playlistSongs);
        $this->table(['id', 'name', 'artist', 'album', 'url'], $playlistSongs);


        $this->info('Done!');
    }
}
