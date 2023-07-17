<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Release;
use App\Services\Scraper\SpotifyMusicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyAppController extends Controller
{
    public function index(Request $request)
    {
        $accessToken = session('spotify_access_token');
        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);
        $options = [
            'limit' => 10,
            'offset' => 0,
        ];
        $spotifyService = new SpotifyMusicService();
        $playlists = $api->getMyPlaylists($options)->items;
        foreach ($playlists as $playlist) {
            $playlist = collect($playlist)->toArray();
            $spotifyService->processPlaylist($playlist, $spotifyService);
        }
        $playlists = $spotifyService->preparePlaylistsTable($playlists);

        $options = [
            'limit' => 30,
            'offset' => 2,
        ];
        $spotifyService = new SpotifyMusicService();
        $playlists2 = $api->getMyPlaylists($options)->items;
        foreach ($playlists2 as $playlist) {
            $playlist = collect($playlist)->toArray();
            $spotifyService->processPlaylist($playlist, $spotifyService);
        }
        $playlists2 = $spotifyService->preparePlaylistsTable($playlists2);

        // combine playlists
        $playlists = array_merge($playlists, $playlists2);

        return new JsonResponse([
            'status ' => 'OK',
           // 'profile' => $api->me(),
            'playlist' => $playlists,
        ]);
    }
}
