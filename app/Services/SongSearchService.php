<?php

namespace App\Services;

use App\Services\Birdy\MeiliSearchService;
use Illuminate\Support\Str;

class SongSearchService
{
    public function searchDb(string $artist, string $title = null)
    {
        $meiliSearchService = new MeiliSearchService();
        $index = $meiliSearchService->getSongIndex();
        $search = $index->search($artist)->getHits();

        if ($title === 'null') {
            // dd($search);
            return $search;
        }
        $title = Str::slug($title, '_');

        return array_filter($search, function ($res) use ($title) {
            if (str_contains($res['title'], $title)) {
                return $res;
            }
        });
    }

    public function searchWebSite(string $site, string $artist, string $title = null)
    {
        $base_search_url = "https://${site}.com/";

        $query = Str::slug($artist, '+');
        if ($site === 'hiphopkit') {
            $search_url = "search?q=${query}&folder=music";
            $url = $base_search_url.$search_url;
            dump($url);
        }
    }
}
