<?php

namespace App\Services\Song;

use App\Models\Song;
use Illuminate\Support\Facades\Http;

class SongRecommendationService
{
    private mixed $fastApiUrl;
    private $init;
    public function __construct()
    {
        $this->fastApiUrl = env('RECO_URL');
        Http::get($this->fastApiUrl . '/api/v1/songs/initialize') ;
        //Http::get( "http://fastapi.curator.atemkeng.eu/api/v1/songs/initialize");
    }

    public function getNearestNeighbor(int $id, int $k, int $l=3): array
    {
        // check if there is a song with this id by finbding the song with id = $id
        $song = Song::query()->find($id);
        if ($song === null) {
            return [
                'error' => 'Song with id: ' . $id . ' not found',
            ];
        }
        $reco_url = $this->fastApiUrl . "/api/v1/songs/search/$id?k=$k&limit=$l";
        //$reco_url = "http://fastapi.curator.atemkeng.eu" . '/api/v1/songs/search/' . $id . '?k=' . $k;
        try {
            $response = Http::get($reco_url);
            $distances = $response->json()['distances'];
            $ids = $response->json()['similar_songs'];
        } catch (\Exception $e) {
            $error=  [
                'error' => 'Error getting recommendation for song with id: ' . $id,
                'message' => $e->getMessage(),
                'fastapi_response' => $response->json(),
                'response_status' => $response->status(),
            ];
            throw new \Exception(json_encode($error));
        }


        // get full song data for each song id from search
        $songs = [];
        $searchSong = new SearchSong();
        $ids = implode(',', $ids);
        $values = '[' . $ids . ']';;
        $searchSong->addQueryFilter('id', 'IN', $values);
        $hits = $searchSong->getSongs()['hits'];

        // show only the attributes we need, author, title, id, key, scale, bpm
        for ($i = 0; $i < count($hits); $i++) {
            // add the corresponding distance to each song
            $songs[] = [
                'id' => $hits[$i]['id'],
                'title' => $hits[$i]['title'],
                'author' => $hits[$i]['author'],
                'key' => $hits[$i]['key'],
                'scale' => $hits[$i]['scale'],
                'bpm' => $hits[$i]['bpm'],
                'path' => $hits[$i]['path'],
                'distance' => $distances[$i],
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