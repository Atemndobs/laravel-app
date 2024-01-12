<?php

namespace App\Services\Song;

use App\Models\Song;
use App\Models\SongKey;
use Illuminate\Support\Facades\Http;
use Spatie\Ray\Settings\Settings;
use TCG\Voyager\Models\Setting;

class SongRecommendationService
{
    private mixed $fastApiUrl;
    public function __construct()
    {
        $this->fastApiUrl = Setting::query()->where('group', 'fastapi')
            ->where('key', 'fastapi.base_url')->first()->value;
        //Http::get($this->fastApiUrl . '/api/v1/songs/initialize') ;
    }

    /**
     * @throws \Exception
     */
    public function getNearestNeighbor(int $id, int $k, int $l=3): array
    {
        // check if there is a song with this id by finding the song with id = $id
        $song = Song::query()->find($id);
        if ($song === null) {
            return [
                'error' => 'Song with id: ' . $id . ' not found',
            ];
        }
        try {
            $response = Http::get($this->fastApiUrl . '/api/v1/songs/search/' . $id . '?k=' . $k );
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
        $bpms = [];
        $keys = [];
        $searchSong = new SearchSong();
        $ids = implode(',', $ids);
        $values = '[' . $ids . ']';;
        $searchSong->addQueryFilter('id', 'IN', $values);
        $hits = $searchSong->getSongs()['hits'];


        // show only the attributes we need, author, title, id, key, scale, bpm
        for ($i = 0; $i < count($hits); $i++) {
            // add the corresponding distance to each song
            $song = $this->getSongFromSearch($hits[$i]['id']);
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
            $bpms[] = $hits[$i]['bpm'];
            $keys[] = $hits[$i]['key'];
            $keyNumbers[] = $song['key_number'];
        }

        return [
            'meta_data' => [
                'k' => $k,
                'l' => $l,
                'song_id' => $id,
                'bpms' => $bpms,
                'keys' => $keys,
                'key_numbers' => $keyNumbers,
            ],
            'songs' => $songs,
            'hits' => $hits,

        ];
      //  return $searchSong->getSongs();
    }

    private function getSongFromSearch(mixed $id)
    {
        /** @var Song $song */
        $song = Song::query()->find($id);
        /** @var SongKey $songKey */
        $songKey = SongKey::query()->where('key_name', $song->key)
            ->where('scale', $song->scale)->first();
        $song = $song->toArray();
        $song['key_number'] = $songKey->key_number;
        return $song;
    }
}