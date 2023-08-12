<?php

namespace App\Services\Birdy;

use App\Jobs\DownloadSpotifyJob;
use App\Models\Song;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyService
{

    public Song $song;

    public SpotifyWebAPI $spotify;

    public function __construct()
    {
        $client_id = env('SPOTIFY_CLIENT_ID');
        $client_secret = env('SPOTIFY_CLIENT_SECRET');
        $url = 'http://dejavu.atmkng.de/';
        $session = new Session(
            'c60869065e4c4a298aaf489700602182',
            '5548d231e4f74c07964d5d675a587c44',
            $url
        );
        $session->requestCredentialsToken();
        $accessToken = $session->getAccessToken();
        $this->spotify = new SpotifyWebAPI();
        $this->spotify->setAccessToken($accessToken);
    }

    public function getArtistGenre(string $artist)
    {
        try {
            $spotifyTrack = $this->spotify->search($artist, 'artist')->artists->items[0];
        }catch (\Exception $e) {
            return 0;
        }
        return $spotifyTrack->genres;
    }

    public function getGenreByArtist(string $author, Song $searchSong)
    {
        $genres = [];
        if ($author === 'unknown') {
            return  ['remix'];
        }

        if ($author == '') {
            return 0;
        }
        $song = Song::query()->where('author', '=', $author)->first(
            ['author', 'title', 'genre']
        );
        if ($song === null) {
            $song = Song::query()->where('author', 'like', "%$author%")->first();
        }
        /** @var Song $song */
        if ((int)$song->genre != 0 && count($song->genre) > 0) {
            $gen = json_encode($song->genre);
            $title = $song->title;
            $searchSong->genre = $song->genre;
            $searchSong->save();
            $searchSong->searchable();
            dump("$title by  $author : $gen");
            return 0;
        }
        if (str_contains($author, ',')) {
            $authors = explode(',', $author);
            foreach ($authors as $author) {
               try {
                   $genres = $this->getGenreByArtist($author, $searchSong);
                   $song->genre = $genres;
                   $song->save();
                   $song->searchable();
                   continue;
               } catch (\Exception $e) {
                   dump($e->getMessage());
               }
            }
        }
        if (str_contains($author, '/')) {
            $authors = explode('/', $author);
            foreach ($authors as $artist) {
                try {
                    $genres = $this->getGenreByArtist($artist, $searchSong);
                    $song->genre = $genres;
                    $song->save();
                    $song->searchable();
                } catch (\Exception $e) {
                    if ($e->getMessage() !== 'The access token expired') {
                        // sleep for a while to avoid rate limit
                        sleep(10);
                        $error = $e->getMessage();
                        dump("Sleeping for 10 seconds to avoid rate limiting ... : Error : $error");
                    } else {
                        dump($e->getMessage());
                        continue;
                    }
                    continue;
                }
            }
        }

        $artists = $this->spotify->search($author, 'artist')->artists->items;
        foreach ($artists as $artist) {
            if (count($artist->genres) === 0) {
                continue;
            }
            if (str_contains($artist->name,$author)){
                $genres[] = [
                    'name' => $artist->name,
                    'genres' => $artist->genres,
                ];
            }

        }
        if (!is_array($genres)) {
            return [];
        }

        if (count($genres) === 1 && isset($genres[0]['name'])) {
            if (str_contains($genres[0]['name'], $author)) {
                return  $genres[0]['genres'];
            }
        }

        if (count($genres) > 1) {
            foreach ($genres as $genre) {

                ray()->clearAll();
                ray([
                    'name' => $genre,
                    'genre' => $genre,
                ])->green();

                if (!isset($genre['name'])) {
                    continue;
                }
                similar_text(strtoupper($author), strtoupper($genre['name']), $perc);

                if ($perc === 100.0) {
                    return $genre['genres'];
                }

                if ($perc >= 80.0) {
                    return $genre['genres'];
                }
            }
        }
        if (count($genres) === 0) {
            return $genres;
        }

        return [];
    }

    public function getSongUlrByTitle(string $title)
    {
        // remove .mp3 from title
        $title = str_replace('.mp3', '', $title);
        // get song url from spotify using $title
        $spotifyTrack = $this->spotify->search($title, 'track')->tracks->items[0];
        $id = $spotifyTrack->id;
        $track = $this->spotify->getTrack($id);
        $artists = $track->artists;
        $artist = $artists[0]->name;
        return $track->external_urls->spotify;
    }

    public function getSpotifySearch(string $query)
    {
        $song = $this->findSong($query);
        if ($song == []){
            return [];
        }
        $url = $song->external_urls->spotify;
        $this->downloadSpotifySong($url);

        return $song;
    }

    public function downloadSpotifySong(string $url)
    {
        DownloadSpotifyJob::dispatch($url);
    }

    /**
     * @param string $query
     * @return array|mixed
     */
    public function findSong(string $query): mixed
    {
        dump([
            'query' => $query,
        ]);
        // artist = first part of query
        $author = explode(' ', $query)[0] ?? $query;
        // title = second part of query
        $title = explode(' ', $query)[1] ?? $query;
        $spotifyTracks = $this->spotify->search($query, 'track')->tracks->items;
        // find track with title or artist matching search query

        Log::warning([
            'spotifyTracks' => $spotifyTracks,
            'query' => $query,
            'file' => __FILE__,
        ]);
        if (count($spotifyTracks) < 1) {
            return [];
        }
        foreach ($spotifyTracks as $spotifyTrack) {
            if (str_contains(strtolower($spotifyTrack->name), $title)) {
                // check if artist is in search $author
                foreach ($spotifyTrack->artists as $artist) {
                    if (str_contains(strtolower($artist->name), $author)) {
                        return $spotifyTrack;
                    }
                }
                return $spotifyTrack;
            }
            elseif (str_contains(strtolower($spotifyTrack->artists[0]->name), $author)) {
                return $spotifyTrack;
            }
        }
        return $spotifyTracks[0];
    }

    public function findSongBySlug(string $slug)
    {
        $song = Song::where('slug', $slug)->first();
        if ($song == null) {
            return [];
        }
        $url = $song->external_urls->spotify;
        $this->downloadSpotifySong($url);
        return $song;
    }

    public function getGenreFromSong(string $searchQuery) : array
    {
        $search = $this->spotify->search($searchQuery, 'track')->tracks->items[0];
        $trackTitle = $search->name;
        $trackArtist = $search->artists[0]->name;
        $genre = $this->getArtistGenre($trackArtist);
        return [
            'title' => $trackTitle,
            'author' => $trackArtist,
            'genre' => $genre,
        ];
    }

    public function getImageFromTitle(string $title)
    {
        $search = $this->spotify->search($title, 'track')->tracks->items[0];
        return $search->album->images[0]->url;
    }
    public function getSongFromTitle(string $title)
    {
        $search = $this->spotify->search($title, 'track')->tracks->items;

        foreach ($search as $song) {
            // unset available_markets
            if (str_contains(strtolower($song->name), trim(strtolower($title)))) {
                $id = $song->id;
                $track = $this->spotify->getTrack($id);
                dump("Found song : " . $track->name);
                dump("Downloading" . $track->external_urls->spotify);
                // call spotify command
                Artisan::call('spotify', [
                    'url' => $track->external_urls->spotify,
                ]);
                return 0;
            }
        }
        return null;
    }

    public function getSongFromArtistAndtitle(string $title)
    {
        $search = $this->spotify->search($title, 'track')->tracks->items;

        foreach ($search as $song) {
            if (str_contains(strtolower($song->album->name), strtolower($title))) {
                return $song;
            }
        }
        return $search[0]->album->name;
    }

    public function getPlaylist(string $playlistName)
    {
        $playlists = $this->spotify->getUserPlaylists($this->getMySpotifyId());
        foreach ($playlists->items as $playlist) {
            if (str_contains(strtolower($playlist->name), strtolower($playlistName))) {
                return $playlist;
            }
        }
        return null;
    }

    public function getMySpotifyId()
    {
        $user = $this->spotify->me();
        return $user->id;
    }

}
