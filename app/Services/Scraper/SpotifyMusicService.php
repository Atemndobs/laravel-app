<?php

namespace App\Services\Scraper;

use Aerni\Spotify\Facades\SpotifyFacade as Spotify;
use App\Models\Release;
use App\Models\SingleRelease;
use AWS\CRT\HTTP\Response;
use Illuminate\Support\Carbon;

class SpotifyMusicService
{

    public function playlist(string $playlistName)
    {
        // 11171774669
        $playlists  = Spotify::userPlaylists('11171774669')->get()['items'];

      //  dd($playlistName);
        $playlistDetails = $this->getItemByName($playlists, $playlistName);

        return $playlistDetails;
    }

    public function userId()
    {
        return Spotify::me()->id;
    }

    public function getItemByName($array, $name) {
        $filteredArray = array_filter($array, function ($item) use ($name) {
            return $item['name'] === $name;
        });

        // Retrieve the first matching item
        $result = reset($filteredArray);

        return $result !== false ? $result : null;
    }

    public function getPlalistNames(array $playlists)
    {
        $names = [];
        foreach ($playlists as $playlist) {
            $names[] = $playlist['name'];
        }
        return $names;
    }

    public function getAllPlaylists()
    {
        return Spotify::userPlaylists('11171774669')->get()['items'];
    }

    public function getPlaylistSongs(string $playlistId)
    {
        return Spotify::playlistTracks($playlistId)->get()['items'];
    }

    public function getPlaylistByName(string $playlistName)
    {
        return Spotify::searchPlaylists($playlistName)->get();
    }

    /**
     * @param array $playlists
     * @return array|array[]
     */
    public function preparePlaylistsTable(array $playlists): array
    {
        return array_map(function ($playlist) {
            return [
                'id' => $playlist['id'],
                'name' => $playlist['name'],
                'owner' => $playlist['owner']['display_name'],
                'tracks' => $playlist['tracks']['total'],
                'url' => $playlist['external_urls']['spotify'],
                'image' => $playlist['images'][0]['url'],
            ];
        }, $playlists);
    }

    /**
     * @param array $tracks
     * @param string $source
     * @return array|array[]
     */
    public function prepareTracksTable(array $tracks, string $source = 'playlist'): array
    {
        return array_map(function ($track) use ($source){
            return [
                'id' => $track['track']['id'],
                'title' => $track['track']['name'],
                'author' => $track['track']['artists'][0]['name'],
                'album' => $track['track']['album']['name'],
                'image' => $track['track']['album']['images'][0]['url'],
                'source' => $source,
                'url' => $track['track']['external_urls']['spotify'],
            ];
        }, $tracks);
    }

    /**
     * @param array $playlist
     * @param Release $release
     * @return void
     */
    public function savePlaylistInDB(array $playlist, Release $release): void
    {
        dd($playlist);
        $release->id = $playlist['id'];
        $release->name = $playlist['name'];
        $release->owner = $playlist['owner'];
        $release->tracks = $playlist['tracks'];
        $release->url = $playlist['url'];
        $release->source = 'spotify';
        $release->type = 'playlist';
        $release->image = $playlist['image'];
        $release->date_created = Carbon::now();
        $release->date_updated = Carbon::now();
        // catch integrity constraint violation and skip
        try {
            $release->saveOrFail();
        } catch (\Throwable $e) {
            // do nothing
        }
    }

    public function getPlaylistSongsByPlaylistId(string $playlistId)
    {
        return Spotify::playlistTracks($playlistId)->get()['items'];
    }

    public function playlistExists(string $id): Release | null
    {
        return Release::query()->where('id', $id)->get()->first();
    }

    public function savePlaylistSongsInDB(array $playlistSongs): void
    {
        foreach ($playlistSongs as $playlistSong) {
            $release = new SingleRelease();
            $release->id = $playlistSong['id'];
            $release->title = $playlistSong['title'];
            $release->author = $playlistSong['author'];
            $release->album = $playlistSong['album'];
            $release->source = $playlistSong['source'];
            $release->url = $playlistSong['url'];
            $release->image = $playlistSong['image'];
            $release->date_created = now();
            $release->date_updated = now();

            // catch integrity constraint violation and skip
            try {
                $release->saveOrFail();
            } catch (\Throwable $e) {
                // do nothing
            }
        }
    }
}
