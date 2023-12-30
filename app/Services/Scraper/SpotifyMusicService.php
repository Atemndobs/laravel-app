<?php

namespace App\Services\Scraper;

use Aerni\Spotify\Facades\SpotifyFacade as Spotify;
use App\Models\Release;
use App\Models\SingleRelease;
use App\Models\Song;
use App\Models\SpotifyAuth;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use League\CommonMark\Extension\CommonMark\Node\Block\ThematicBreak;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyMusicService
{
    private SpotifyWebAPI $spotify;
    private User $user;
    private string $spotifyId;

    public function __construct()
    {
        $this->user = (new User())->getLoggedInUser();
        $this->spotify = new SpotifyWebAPI();
        $spotifySession  = json_decode($this->user->session, true);
        $accessToken = $spotifySession['access_token'];
        $this->spotify->setAccessToken($accessToken);
        $this->spotifyId = $this->spotify->me()->id;
    }

    public function playlist(string $playlistName, string $owner)
    {
        $playlists = $this->getMyPlaylistByName($playlistName);
        if ($playlists) {
            return $playlists;
        } else {
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
                'tracks' => $playlist['tracks'] ?? $playlist['tracks']['total'],
                'url' => $playlist['external_urls'] ?? $playlist['external_urls']['spotify'],
                'image' => $playlist['images'][0] ?? $playlist['images'][0]['url'],
            ];
        } catch (\Exception $e) {
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

    public function getItemByName($array, $name, $songArtist = null)
    {
        $filteredArray = array_filter($array, function ($item) use ($name, $songArtist) {
            if (is_object($item)) {
                $item_name = $item->name;
                $artists = $item->artists;
            }else {
                $item_name = $item['name'];
                $artists = $item['artists'];
            }

            $item_name = strtolower($item_name);
            $name = strtolower($name);
            // remove spaces before and after
            $item_name = trim($item_name);
            $name = trim($name);

            if ($songArtist) {
                if ($item_name === $name && trim(strtolower($songArtist)) === trim(strtolower($artists[0]->name))) {
                    return true;
                }
            }

            $artistNames = $this->getArtistNames($artists);

            if ($item_name === $name) {
                if ($songArtist) {
                    if (in_array($songArtist, $artistNames)) {
                        return true;
                    }
                } else {
                    // check if one of the names in artistNames is contained in songArtist
                    foreach ($artistNames as $artistName) {
                        if (str_contains($songArtist, $artistName)) {
                            return true;
                        }
                    }
                }

            }

            return false;
        });

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
        $total = (int)Spotify::userPlaylists($this->spotifyId)->get()['total'];
        // if total is greater than 50, we need to paginate
        if ($total > 50) {
            $playlists = $this->paginatePlaylists($total);
        } else {
            $playlists = Spotify::userPlaylists($this->spotifyId)->limit(50)->get()['items'];
        }
        // remove playlists named 'Remixers' and 'ATM Release Radar'
        return array_filter($playlists, function ($playlist) {
            return $playlist['name'] !== 'Remixers' && $playlist['name'] !== 'ATM Release Radar';
        });
    }

    public function getPlaylistSongs(string $playlistId)
    {
        return Spotify::playlistTracks($playlistId)->get()['items'];
    }

    public function getPlaylistByName(string $playlistName, string $owner)
    {
        dump([
            'playlistName' => $playlistName,
            'owner' => $owner,
        ]);
        $playlists = Spotify::searchPlaylists($playlistName)->get();
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

        return array_map(function ($track) use ($source) {
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
            } catch (\Exception $e) {
                $error = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ];
                Log::error(json_encode($error, JSON_PRETTY_PRINT));
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

        try {
            !is_array($playlist['owner']) ? $playlist['owner'] = collect($playlist['owner'])->toArray()['display_name'] : $playlist['owner'];
            !is_array($playlist['tracks']) ? $playlist['tracks'] = collect($playlist['tracks'])->toArray()['total'] : $playlist['tracks'];
            !is_array($playlist['image']) ? $playlist['image'] = collect($playlist['image'])->toArray()['url'] : $playlist['image'];
            !is_array($playlist['url']) ? $playlist['url'] = collect($playlist['url'])->toArray()['spotify'] : $playlist['url'];

            // remove emojis from name
            $name = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $playlist['name']);
            $release->id = $playlist['id'];
            $release->name = $name;
            $release->owner = $playlist['owner'];
            $release->tracks = $playlist['tracks'];
            $release->url = $playlist['url'];
            $release->source = 'spotify';
            $release->type = 'playlist';
            $release->image = $playlist['image'];
            $release->date_created = now();
            $release->date_updated = now();

            $release->save();
        } catch (\Throwable $e) {
            $error = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
            Log::error(json_encode($error, JSON_PRETTY_PRINT));
        }
    }

    public function getPlaylistSongsByPlaylistId(string $playlistId, int $offset = 0, int $limit = 100)
    {
        return Spotify::playlistTracks($playlistId)->limit($limit)->offset($offset)->get()['items'];
    }


    public function playlistExists(string $id): Release|null
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
                // if song exists, skip
                if ($exists->count() > 0) {
                    continue;
                }
            } catch (\Exception $e) {
                $error = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ];
                Log::error(json_encode($error, JSON_PRETTY_PRINT));
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
                $error = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ];
                Log::error(json_encode($error, JSON_PRETTY_PRINT));
                continue;
            }
        }
    }

    public function playlistSongsExists(array $playlistSongs): array|null
    {
        $ids = [];
        foreach ($playlistSongs as $playlistSong) {
            if (empty($playlistSong['id'])) {
                continue;
            }
            $ids[] = $playlistSong['id'];
        }
        return SingleRelease::query()->whereIn('id', $ids)->get()->toArray();
    }

    private function paginatePlaylists(int $total)
    {
        $playlists = [];
        $offset = 0;
        while ($offset < $total) {
            $playlists[] = Spotify::userPlaylists($this->spotifyId)->limit(50)->offset($offset)->get()['items'];
            $offset += 50;
        }
        return $playlists[0];
    }

    public function getMyPlaylistByName(string $playlistName)
    {
        $playlists = Spotify::userPlaylists($this->spotifyId)->get()['items'];
        if (empty($playlists)) {
            return null;
        }

        foreach ($playlists as $playlist) {
            if (trim($playlist['name']) === $playlistName) {
                return $playlist;
            }
        }
        return null;
    }

    public function getMyFollowedPlaylistsByName(string $playlistName)
    {
        $playlists = Spotify::myPlaylists()->get()['items'];
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
     * @param array $playlist
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
            } catch (\Throwable $e) {
                $playlistTrackCount = $playlist['tracks']->total;
            }

            if ($tracksCount == $playlistTrackCount) {
                $this->checkAndSavePlaylistSongs($playlist['id'], $spotifyService);
                return;
            }

            $tracksCount = $playlist['tracks']->total ?? $playlist['tracks']['total'];
            $playlistExists->tracks = $tracksCount;
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
     * @param string $playlistId
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
        // Add the song to the Release Radar Playlist
    }

    /**
     * Add a song to the Release Radar playlist.
     *
     * @param int $addedSince
     * @param Date|null $from
     * @param Date|null $until
     * @return array
     */
    public function getRecentlyAddedSongs(int $addedSince, Date $from = null, Date $until = null): array
    {
        $dateSince = Carbon::now()->subHours($addedSince);
        $formattedDateTime = $dateSince->format('Y-m-d\TH:i:s.u\Z');
        if ($from && $until) {
            $playlistSongs = SingleRelease::query()->whereBetween('added_at', [$from, $until])->get();
        } else {
            $playlistSongs = SingleRelease::query()->where('added_at', '>=', $formattedDateTime)->get();
        }

        $songIds = [];
        foreach ($playlistSongs as $playlistSong) {
            $songIds[] = $playlistSong->id;
        }
        return $songIds;
    }

    public function addSongToReleaseRadar(array $songIds, string $releaseRadarName = 'ATM Release Radar'): string
    {
        try {
            $releaseRadarPlaylistId = Release::query()->where('name', $releaseRadarName)->first()->id;
        }catch (\Throwable $e){
            $message = [
                'message' => 'Release Radar Playlist not found-- Getting playlist ID from Spotify',
            ];
            Log::warning('Release Radar Playlist not found-- Getting playlist ID from Spotify');
            // get Playlist by name
            $playlist = $this->getMyPlaylistByName($releaseRadarName);
            if ($playlist == null) {
                return json_encode($message, JSON_PRETTY_PRINT);
            }
            $releaseRadarPlaylistId = $playlist['id'];
        }
        $api = $this->spotify;
        $api->addPlaylistTracks($releaseRadarPlaylistId, $songIds);
        $playlist = $api->getPlaylist($releaseRadarPlaylistId);
        $tracksCount = $playlist->tracks->total;
        $songsAdded = count($songIds);
        $message = [
            'message' => 'Songs added to Release Radar',
            'Songs in Release Radar Playlist' => $tracksCount,
            'Songs Added' => $songsAdded,
        ];
        return json_encode($message, JSON_PRETTY_PRINT);
    }

    public function searchSongByTitleAndArtist(string $title, string $artist)
    {
        $api = $this->spotify;
        $search = $api->search($title, 'track');
        $tracks = $search->tracks->items;
        $track = $this->getItemByName($tracks, $title, $artist);

        if ($track == null) {
            return null;
        }

        return $track->id;
    }

    private function getArtistNames(mixed $artists): array
    {
        $artistNames = [];
        foreach ($artists as $artist) {
            $artistNames[] = $artist->name;
        }
        return $artistNames;
    }

    public function getImage(mixed $song_id)
    {
        $track = $this->spotify->getTrack($song_id);
        $images = $track->album->images;
        return $images[0]->url;
    }

    public function getGenre(mixed $song_id)
    {
        $api = $this->spotify;
        $track = $api->getTrack($song_id);
        $album = $api->getAlbum($track->album->id);
        if (empty($album->genres)) {
            // get genre from artist
            $artist = $api->getArtist($track->artists[0]->id);
            return $artist->genres;
        }
        return $album->genres;
    }

    public function getTitle(bool|array|string|null $song_id)
    {
        $track = $this->spotify->getTrack($song_id);
        return $track->name;
    }

    public function getArtists(bool|array|string|null $song_id)
    {
        $track = $this->spotify->getTrack($song_id);
        $artists = $track->artists;
        $artistNames = [];
        foreach ($artists as $artist) {
            $artistNames[] = $artist->name;
        }
        // return artists as a comma separated string if the array is not empty
        return implode(', ', $artistNames);

    }

    public function searchSong(string $title, string $artist)
    {
        $search = $this->spotify->search($title, 'track');
        $tracks = $search->tracks->items;

        if (empty($tracks)) {
            return null;
        }
        if ($artist == null) {
            $trackResults = [];
            foreach ($tracks as $track) {
                if ($track->name == $title) {
                    $trackResults[] = $this->getTrackArray($track);
                }
            }
            return $trackResults;
        }
        $track = $this->getItemByName($tracks, $title, $artist);
        if ($track == null) {
            return null;
        }
        return $this->getTrackArray($track);
    }

    public function getLikedSongsIds(int $time , int $limit) : array
    {
        $spotifyLikedSongs = [];
        // search saved tracks since 24 hours ago
        $since = Carbon::now()->subHours($time);
        $formattedDateTime = $since->format('Y-m-d\TH:i:s.u\Z');

        // get total liked songs
        $totalLikedSongs = $this->spotify->getMySavedTracks(['limit' => 1])->total;

        if ($totalLikedSongs > 50 && $limit > 50) {
            $limit = 50;
        }
        $page = 0;
        // find all liked songs for the total liked songs
        while ($page * $limit < $totalLikedSongs) {
            $myLikedSongs = $this->spotify->getMySavedTracks(['limit' => $limit, 'offset' => $page * $limit]);

            $myRecentLikedSongs = collect($myLikedSongs->items)->filter(function ($item) use ($formattedDateTime) {
                return $item->added_at >= $formattedDateTime;
            });
            foreach ($myRecentLikedSongs as $item) {
                $spotifyLikedSongs[] = $this->getTrackArray($item->track);
            }
            if ($myLikedSongs->next == null) {
                break;
            }
            if (count($myRecentLikedSongs) < $limit) {
                break;
            }

            $page++;
        }

        return $spotifyLikedSongs;
    }

    public function checkIfSongExists(mixed $likedSongId): bool
    {
        $song = Song::query()->where('song_id', $likedSongId)
            ->where('source', 'spotify')
            ->first();
        if ($song == null) {
            return false;
        }
        return true;
    }

    /**
     * @param $track
     * @return array
     */
    public function getTrackArray($track): array
    {
        return [
            'id' => $track->id,
            'title' => $track->name,
            'artist' => implode(',', $this->getArtistNames($track->artists)),
            'image' => $track->album->images[0]->url,
            'genre' => $this->getGenre($track->id),
            'url' => $track->external_urls->spotify,
            'share_url' => $track->external_urls->spotify,
        ];
    }
    public function fetchSongsInRange(string $playlistId, int $start, int $end)
    {
        $limit = $end - $start + 1;
        $offset = $start - 1;

        // Fetch the songs from the specified range
        $songs = $this->getPlaylistSongsByPlaylistId($playlistId, $offset, $limit);

        // Process the songs as needed
        // ...

        return $songs;
    }
    public function getSpotifyIdsFromPlaylist(string $playlist, int $offset, int $limit)
    {
        if (str_contains($playlist, 'playlist')) {
            $playlistId = explode("playlist/", $playlist);
            $playlistId = $playlistId[1];
            $playlistId = explode("?", $playlistId);
            $playlistId = $playlistId[0];
        } else {
            // if $playlist looks like 6L395PhP6WoQIotqLYg7lQ then its a playlist id
            if (strlen($playlist) == 22) {
                $playlistId = $playlist;
            } else {
                try {
                    $playlist = $this->getMyPlaylistByName($playlist);
                    $playlistId = $playlist['id'];
                } catch (\Exception $e) {
                    throw new \Exception('Playlist not found in your Spotify account. IT should be a playlist URL or a playlist ID.');
                }
            }
        }
        $playlistSongs = $this->getPlaylistSongsByPlaylistId($playlistId, $offset, $limit);
        // count all songs in playlist
        $totalSongs = $this->spotify->getPlaylist($playlistId)->tracks->total;
        Log::info('Total songs in playlist: ' . $totalSongs);
        $spotifyIds = [];
        foreach ($playlistSongs as $playlistSong) {
            $songExists = Song::query()->where('song_id', $playlistSong['track']['id'])->first();
            if ($songExists) {
                Log::warning('Song with ID ' . $playlistSong['track']['id'] . ' already exists in DB.');
                dump('Song with ID ' . $playlistSong['track']['id'] . ' already exists in DB.');
                continue;
            }
            $spotifyIds[] = $playlistSong['track']['id'];
        }
        if (empty($spotifyIds)) {
            Log::warning('All songs in playlist already exist in DB.');
            throw new \Exception('All songs in playlist already exist in DB.');
        }
        return [
            'spotifyIds' => $spotifyIds,
            'url' => $playlist['external_urls']['spotify'],
        ];
    }
}

