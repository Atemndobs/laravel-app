<?php

namespace App\Services\Scraper;

use Aerni\Spotify\Facades\SpotifyFacade as Spotify;
use App\Models\Release;
use App\Models\SingleRelease;
use Illuminate\Support\Carbon;

class SpotifyMusicService
{

    public function playlist(string $playlistName, string $owner)
    {
        // 11171774669
        $playlists = $this->getMyPlaylistByName($playlistName);
        if($playlists){
            return $playlists;
        }else{
            return $this->getPlaylistByName($playlistName, $owner);
        }

    }

    public function prepareSinglePlaylistTable(array $playlist): array
    {
        try {
            return [
                'id' => $playlist['id'],
                'name' => $playlist['name'],
                'owner' => $playlist['owner'] ?? $playlist['owner']['display_name'],
                'tracks' => $playlist['tracks']?? $playlist['tracks']['total'],
                'url' => $playlist['external_urls']?? $playlist['external_urls']['spotify'],
                'image' => $playlist['images'][0]?? $playlist['images'][0]['url'],
            ];
        }catch (\Exception $e) {
            return [
                'id' => $playlist['id'],
                'name' => $playlist['name'],
                'owner' => $playlist['owner']->display_name,
                'tracks' => $playlist['tracks']->total,
                'url' => $playlist['external_urls']->spotify,
                'image' => $playlist['images'][0]->url,
            ];
        }

    }

    public function getItemByName($array, $name) {
        $filteredArray = array_filter($array, function ($item) use ($name) {
            return $item['name'] === $name;
        });

        // Retrieve the first matching item
        $result = reset($filteredArray);

        return $result !== false ? $result : null;
    }

    public function getPlaylistNames(array $playlists): array
    {
        $names = [];
        foreach ($playlists as $playlist) {
            $names[] = $playlist['name'];
        }
        return $names;
    }

    public function getAllPlaylists()
    {
        $total = (int)Spotify::userPlaylists('11171774669')->get()['total'];
        // if total is greater than 50, we need to paginate
        if ($total > 50) {
            $playlists = $this->paginatePlaylists($total);
        } else {
            $playlists = Spotify::userPlaylists('11171774669')->limit(50)->get()['items'];
        }
        return $playlists;
    }

    public function getPlaylistSongs(string $playlistId)
    {
        return Spotify::playlistTracks($playlistId)->get()['items'];
    }

    public function getPlaylistByName(string $playlistName, string $owner)
    {
        $playlists =  Spotify::searchPlaylists($playlistName)->get();
        $playlists = $playlists['playlists']['items'];
        return $this->getItemByNameAndOwner($playlists, $playlistName, $owner);
    }

    /**
     * @param array $playlists
     * @return array|array[]
     */
    public function preparePlaylistsTable(array $playlists): array
    {
        return array_map(function ($playlist) {
            !is_array($playlist) ? $playlist = collect($playlist)->toArray() : $playlist;
            !is_array($playlist['owner']) ? $playlist['owner'] = collect($playlist['owner'])->toArray() : $playlist['owner'];
            !is_array($playlist['tracks']) ? $playlist['tracks'] = collect($playlist['tracks'])->toArray() : $playlist['tracks'];
            !is_array($playlist['external_urls']) ? $playlist['external_urls'] = collect($playlist['external_urls'])->toArray() : $playlist['external_urls'];
            !is_array($playlist['images'][0]) ? $playlist['images'][0] = collect($playlist['images'][0])->toArray() : $playlist['images'][0];

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
            try {
                return [
                    'id' => $track['track']['id'],
                    'title' => $track['track']['name'],
                    'author' => $track['track']['artists'][0]['name'],
                    'album' => $track['track']['album']['name'],
                    'added_at' => $track['added_at'],
                    'source' => $source,
                    'url' => $track['track']['external_urls']['spotify'],
                    'image' => $track['track']['album']['images'][0]['url'] ?? null,
                ];
            }catch (\Exception $e) {

            }
        }, $tracks);
    }

    /**
     * @param array $playlist
     * @param Release $release
     * @return void
     */
    public function savePlaylistInDB(array $playlist, Release $release): void
    {
        // check if playlist exists
        $exists = $this->playlistExists($playlist['id']);
        if ($exists) {
            return;
        }
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
            // check if song already exists
            $exists = null;
            try {
                $exists = SingleRelease::query()->where('id', $playlistSong['id'])->get();
            }catch (\Exception $e) {
//                dump ([
//                    'Error' => $e->getMessage(),
//                    'Playlist' => $playlistSong
//                ]);
                continue;
            }
            if ($exists) {
                continue;
            }
            try {
                $release->id = $playlistSong['id'];
                $release->title = $playlistSong['title'];
                $release->author = $playlistSong['author'];
                $release->album = $playlistSong['album'];
                $release->source = $playlistSong['source'];
                $release->url = $playlistSong['url'];
                $release->image = $playlistSong['image'];
                $release->added_at = $playlistSong['added_at'];
                $release->date_created = now();
                $release->date_updated = now();
                $release->save();
            } catch (\Throwable $e) {
//                dump ([
//                    'Error' => $e->getMessage(),
//                    'Playlist' => $playlistSong
//                ]);
                continue;
            }
        }
    }

    public function playlistSongsExists(array $playlistSongs): array | null
    {
        $ids = [];
        foreach ($playlistSongs as $playlistSong) {
            $ids[] = $playlistSong['id'];
        }
        return SingleRelease::query()->whereIn('id', $ids)->get()->toArray();
    }

    private function paginatePlaylists(int $total)
    {
        $playlists = [];
        $offset = 0;
        while ($offset < $total) {
            $playlists[] = Spotify::userPlaylists('11171774669')->limit(50)->offset($offset)->get()['items'];
            $offset += 50;
        }
        return $playlists[0];
    }

    public function getMyPlaylistByName(string $playlistName)
    {
        $playlists  = Spotify::userPlaylists('11171774669')->get()['items'];
        if (empty($playlists)) {
            return null;
        }
        return $this->getItemByName($playlists, $playlistName);
    }

    public function getMyFollowedPlaylistsByName(string $playlistName)
    {
        $playlists  = Spotify::myPlaylists()->get()['items'];
        return $this->getItemByName($playlists, $playlistName);
    }

    private function getItemByNameAndOwner(mixed $items, string $playlistName, string $owner)
    {
        $filteredArray = array_filter($items, function ($item) use ($playlistName, $owner) {
            return trim($item['name']) === $playlistName && trim($item['owner']['display_name']) === $owner;
        });
        // Retrieve the first matching item
        $result = reset($filteredArray);

        return $result !== false ? $result : null;
    }


    /**
     * Process a playlist.
     *
     * @param array              $playlist
     * @param SpotifyMusicService $spotifyService
     */
    public function processPlaylist(array $playlist, SpotifyMusicService $spotifyService): void
    {
        $releases = new Release();
        $playlistExists = $spotifyService->playlistExists($playlist['id']);

        if ($playlistExists) {
            $tracksCount = $playlistExists->tracks;
            try {
                $playlistTrackCount = $playlist['tracks']['total']['total'] ?? $playlist['tracks']['total'];
            }catch (\Throwable $e) {
                $playlistTrackCount = $playlist['tracks']->total;
            }

            if ($tracksCount == $playlistTrackCount) {
                $this->checkAndSavePlaylistSongs($playlist['id'], $spotifyService);
                return;
            }
            $playlistExists->tracks = $playlist['tracks']['total'];
            $playlistExists->save();
        } else {
            $playlistTable = $spotifyService->prepareSinglePlaylistTable($playlist);
            $spotifyService->savePlaylistInDB($playlistTable, $releases);
        }

        $playlistSongs = $spotifyService->getPlaylistSongs($playlist['id']);
        $playlistSongs = $spotifyService->prepareTracksTable($playlistSongs);
        $spotifyService->savePlaylistSongsInDB($playlistSongs);

    }

    /**
     * Check if playlist songs are the same and save them if necessary.
     *
     * @param string             $playlistId
     * @param SpotifyMusicService $spotifyService
     */
    protected function checkAndSavePlaylistSongs(string $playlistId, SpotifyMusicService $spotifyService): void
    {
        $playlistSongs = $spotifyService->getPlaylistSongs($playlistId);
        $playlistSongs = $spotifyService->prepareTracksTable($playlistSongs);
        $playlistSongsExists = $spotifyService->playlistSongsExists($playlistSongs);

        if ($playlistSongsExists) {
            return;
        }
        $spotifyService->savePlaylistSongsInDB($playlistSongs);
    }

}
