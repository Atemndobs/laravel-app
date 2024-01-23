<?php

namespace App\Services\Recommendation;

use App\Models\Song;
use App\Services\Song\SearchSong;
use Illuminate\Support\Facades\Http;

class SpotifyRecommendationService
{
    public function getChosicRecommendation(string $songId, int $limit = 100): array
    {
        $endpoint = "https://www.chosic.com/api/tools/recommendations?seed_tracks=$songId&limit=$limit";
        $header = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authority' => 'www.chosic.com',
            'app' => 'playlist_generator',
            'cookie' => 'pll_language=en',
            'referer' => 'https://www.chosic.com/playlist-generator/',
        ];
        $response = Http::withHeaders($header)->get($endpoint);
        $response = json_decode($response->body(), true);
        $songs = $response['tracks'];
        $songs = array_map(function ($song) {
            return [
                'id' => $song['id'],
                'title' => $song['name'],
                'author' => $song['artists'][0]['name'],
//                'key' => $song['key'],
//                'scale' => $song['mode'],
//                'bpm' => $song['tempo'],
//                'energy' => $song['energy'],
//                'danceability' => $song['danceability'],
//                'valence' => $song['valence'],
//                'acousticness' => $song['acousticness'],
//                'instrumentalness' => $song['instrumentalness'],
//                'liveness' => $song['liveness'],
//                'speechiness' => $song['speechiness'],
//                'duration_ms' => $song['duration_ms'],
//                'popularity' => $song['popularity'],
//                'spotify_url' => $song['external_urls']['spotify'],
//                'spotify_id' => $song['id'],
//                'spotify_preview_url' => $song['preview_url'],
//                'spotify_uri' => $song['uri'],
//                'spotify_album_id' => $song['album']['id'],
//                'spotify_album_name' => $song['album']['name'],
//                'spotify_album_type' => $song['album']['type'],
//                'spotify_album_release_date' => $song['album']['release_date'],
//                'spotify_album_release_date_precision' => $song['album']['release_date_precision'],
//                'spotify_album_total_tracks' => $song['album']['total_tracks'],
//                'spotify_album_art_url' => $song['album']['images'][0]['url'],
//                'spotify_album_art_width' => $song['album']['images'][0]['width'],
//                'spotify_album_art_height' => $song['album']['images'][0]['height'],
            ];
        }, $songs);
        return $songs;
    }

    /**
     * @param array $ids
     * @param $limit
     * @return array
     */
    public function matchRecommendation(array $ids, $limit = null)
    {
        $songSearch = new SearchSong();
        $songs = Song::query()->whereIn('song_id', array_column($ids, 'id'))->get('id');
        $ids = array_column($songs->toArray(), 'id');
        $filterString = implode(" OR ", array_map(function($id) {
            return "id = $id";
        }, $ids));
        $searchParams = ['filter' => $filterString];
        $limit = $limit ?? count($ids);

       // dd($searchParams, $limit);
        $foundSongs = $songSearch->getSongs(0, $limit, $searchParams);

        $count = count($songs);
        return [
            'songs' => $foundSongs,
            'total_songs' => $count,
            'limit' => $limit,
        ];
    }

}