<?php

namespace App\Services\Song;

use Illuminate\Support\Facades\Http;

class SongRecommendationService
{
    private $fastApiUrl;
    private $init;
    public function __construct()
    {
        $this->fastApiUrl = env('RECO_URL');
        $this->init = env('RECO_URL' . '/api/v1/initialize');
    }

    public function getNearestNeighbor(int $id, int $k )
    {
        $reco_url = $this->fastApiUrl . '/api/v1/search/' . $id . '?k=' . $k;
        // get recommendation : http://fastapi/api/v1/search/98?k=$k
        $response = Http::get($reco_url);
        $distances = $response->json()['distances'];
        $ids = $response->json()['similar_songs'];
        // get full song data for each song id from search
        $songs = [];
        $searchSong = new SearchSong();
        $ids = implode(',', $ids);
        $values = '[' . $ids . ']';;
        $searchSong->addQueryFilter('id', 'IN', $values);
        $hits = $searchSong->getSongs()['hits'];

        // show only the attributes we need, author, title, id, key, scale, bpm
        foreach ($hits as $hit) {
            $songs[] = [
                'id' => $hit['id'],
                'title' => $hit['title'],
                'author' => $hit['author'],
                'key' => $hit['key'],
                'scale' => $hit['scale'],
                'bpm' => $hit['bpm'],
            ];
        }

        return [
            'songs' => $songs,
            'distances' => $distances,
            'ids' => $ids,
        ];
      //  return $searchSong->getSongs();
    }
}