<?php

namespace App\Services\Scraper;

use App\Models\Song;
use Goutte\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class SoundcloudService
{
    use Tools;
    public Client $client;

    private string $baseUrl;

    private string $client_id;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = 'https://soundcloud.com';
        $this->client_id = 'riOYGQKQTbmJxmNN9XiBsc8LSF4RRT7F';
    }

    /**
     * @param  string  $artist
     * @param  string  $searchTerm
     * @return array[]
     */
    public function getLikedSongsByArtis(string $artist, string $searchTerm = 'likes'): array
    {
        $url = "$this->baseUrl/$artist/$searchTerm";
        $res = $this->client->request('GET', $url);

        $songLinks = $res->filter('a')->each(function ($node) {
            return $node->attr('href').'';
        });
        $songLinks = array_unique($songLinks);

        foreach ($songLinks as $key => $songLink) {
            if ($songLink === "/$artist"
                || $songLink === '/'
                || $songLink === 'http://www.enable-javascript.com/'
                || $songLink === 'https://help.soundcloud.com'
                || $songLink === 'http://windows.microsoft.com/ie'
                || $songLink === 'http://apple.com/safari'
                || $songLink === 'http://firefox.com'
                || $songLink === 'http://google.com/chrome'
                || $songLink === '/popular/searches'
                || $songLink === 'https://help.soundcloud.com/hc/articles/115003564308-Technical-requirements'
            ) {
                unset($songLinks[$key]);
            }
        }

        $likedArtists = [];
        $likedSongs = [];
        foreach ($songLinks as $songLink) {
            if (substr_count($songLink, '/') < 2) {
                $likedArtists[] = $this->baseUrl.$songLink;
            } else {
                $song = $this->baseUrl.$songLink;
                $likedSongs[] = $song;
                dump($song);

                if (! $this->existing($song)) {
                    $this->downloadSong($song);
                    sleep(5);
                } else {
                    dump("Skipping $songLink");
                }
            }
        }

        return  [
            'artists' => $likedArtists,
            'liked_songs' => $likedSongs,
        ];
    }

    public function getArtistPlaylists(string $artist): array
    {
        $url = "$this->baseUrl/$artist";
        $res = $this->client->request('GET', $url);

        $songLinks = $res->filter('a')->each(function ($node) {
            return $node->attr('href').'';
        });
        $songLinks = array_unique($songLinks);

        $playlists = [];
        foreach ($songLinks as $link) {
            if (str_contains($link, 'comments')) {
                continue;
            }
            if (substr_count($link, '/') < 2) {
                continue;
            }
            if (str_contains($link, $artist)) {
                $playlists[] = $link;
            }
        }

        return $playlists;
    }

    public function getCuratedPlaylist(string $artist = 'theafrobeatshub'): array
    {
        $url = $this->baseUrl.'/'.$artist;

        $res = $this->client->request('GET', $url);
        $songLinks = $res->filter('a')->each(function ($node) {
            $link = $node->attr('href').'';
            if (
                substr_count($link, '/') < 2
                || str_contains($link, '/likes')
                || str_contains($link, '/sets')
                || str_contains($link, '/tracks')
                || str_contains($link, '/comments')
                || str_contains($link, 'Technical-requirements')
                || str_contains($link, '/popular/searches')
                || str_contains($link, 'firefox')
                || str_contains($link, 'safari')
                || str_contains($link, 'chrome')
                || str_contains($link, 'javascript')
                || str_contains($link, 'microsoft.com/ie')
                || str_contains($link, 'help.soundcloud.com')

            ) {
                return 0;
            }

            return $link;
        });

        $songLinks = array_unique($songLinks);

        foreach ($songLinks as $songLink) {
            if ($songLink !== 0 && ! $this->existing($songLink)) {
                $song = $this->baseUrl.$songLink;
                $this->downloadSong($song);
                sleep(5);
            } else {
                dump("Skipping $songLink");
            }
        }

        return  $songLinks;
    }

    public function getTrackLink(string $searchQuery, array $params= [])
    {
        $baseUrl = "https://soundcloud.com";
        $searchUrl = "$baseUrl/search?q=$searchQuery";
        $songLinks = $this->getSongLinks($searchUrl);
        $songLinks = $songLinks[0];
        $foundArtist = explode('/', $songLinks)[1];
        $foundTitle = explode('/', $songLinks)[2];
        $paramArtist = $params[1];
        $paramTitle = $params[0];

        // if the found title contains the param title return the song link
        if (str_contains($foundTitle, $paramTitle)) {
            dump([
                "FOUND LINK ====" => $songLinks,
            ]);
            return $baseUrl . $songLinks;
        }

        return 0;
    }

    public function downloadSong(string $url)
    {
        $strapi_url = 'http://host.docker.internal:1337/api/classify?link=';
        $link = $strapi_url.$url;
        $response = Http::get($link);

        return $response->status();
    }

    /**
     * @param  string  $title
     * @return mixed
     */
    public function existing(string $title)
    {
        $check = explode('/', $title);
        $n = count($check);

        $slug = '';
        foreach ($check as $i => $iValue) {
            if ($i === $n - 1) {
                $slug = Str::slug($iValue.'mp3', '_');
            }
        }

        return  Song::where('slug', '=', $slug)->first();
    }

    public function downloadPlaylist(string $songLink)
    {
        ray($songLink)->red();
        $song = $this->baseUrl.$songLink;

        return $this->downloadSong($song);
    }

    /**
     * @param string $authorLink
     * @return string
     */
    public function extractAuthorFromLink(string $authorLink): string
    {
        $authorData = $this->client->request('GET', $authorLink);
        return $authorData->filter('h1')->text();
    }

    public function extractSoundcloudSongId($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $path = ltrim($path, '/');
        return str_replace('/', '_', $path);
    }

    /**
     * @param string $trackLink
     * @return string
     */
    public function extractAuthorFromTrackLink(string $trackLink): string
    {
        $soundcloudService = new SoundcloudService();
        $authorLink = explode('/', $trackLink);
        $n = count($authorLink);
        unset($authorLink[$n - 1]);
        $authorLink = implode('/', $authorLink);
        return $soundcloudService->extractAuthorFromLink($authorLink);
    }

}
