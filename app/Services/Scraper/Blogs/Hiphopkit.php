<?php

namespace App\Services\Scraper\Blogs;

use App\Services\Scraper\Tools;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Hiphopkit
{
    use Tools;

    /**
     * @param  string  $artist
     * @return array
     */
    public function getSongsFromHiphopkit(string $artist)
    {
        $artist = Str::slug($artist, '-');
        $baseUrl = 'https://hiphopkit.com/';
        $downloadLink = $baseUrl.'music/artiste/'.$artist;
        $res = $this->client->request('GET', $downloadLink);

        $songLinks = $res->filter('a')->each(function ($node) {
            return $node->attr('href').'';
        });

        ray()->clearAll();

        $songLinks = array_unique($songLinks);
        $collectedSongs = [];
        foreach ($songLinks as $key => $songLink) {
            if (

                $songLink == $baseUrl
                //   || $songLink == $downloadLink
                //  || !str_contains($songLink, "$baseUrl$artist")
            ) {
                unset($songLinks[$key]);
            }
        }

        ray([
            'base_url' => $baseUrl,
            'download_link' => $downloadLink,
            'Artist' => $artist,
            'links' => $songLinks,
        ]);
        foreach ($songLinks as $songLink) {
            $link = $this->client->request('GET', $songLink);
            $artistLinks = $link->filter('a')->each(function ($node) {
                $link = $node->attr('href').'';

                return str_contains($link, 'music/download') ? $link : null;
            });
            $dnloadMusic = array_unique($artistLinks);

            foreach ($dnloadMusic as $music) {
                if ($music !== null) {
                    ray($music)->screenGreen();
                    $songId = substr($songLink, -4);
                    $title = str_replace(['https://hiphopkit.com/', $songId], '', $songLink);
                    $title = Str::slug($title, '_');
                    $path = "storage/app/public/audio/$title.mp3";
                    $exist = Storage::exists("public/audio/$title.mp3");

                    ray($path)->red();
                    if (! $exist) {
                        $this->download($path, $music);
                        file_put_contents($path, fopen($music, 'r'));
                        $collectedSongs[] = asset("storage/audio/$title.mp3");
                    }

                    ray($collectedSongs)->screenGreen();

                    if (count($collectedSongs) === 5) {
                        break;
                    }
                }
            }
        }

        return $collectedSongs;
    }
}
