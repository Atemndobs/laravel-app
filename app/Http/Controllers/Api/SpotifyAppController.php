<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Release;
use App\Models\SpotifyAuth;
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
        return $api->me();

        $spotifyService = new SpotifyMusicService();

        $playlists = [];
        $options = [
            'limit' => 1,
            'offset' => 0,
        ];
        $getPlaylists = function ($options) use ($api) {
            try {
                $api->getMyPlaylists($options);
            } catch (\Exception $e) {
                // truncate Spotify Auth table
                SpotifyAuth::truncate();
                $accessToken = session('spotify_access_token');
                $api = new SpotifyWebAPI();
                $api->setAccessToken($accessToken);

            }
            return $api->getMyPlaylists($options);
        };
        $totalPlaylists = $getPlaylists($options)->total;
        
        // get limit and offset from request
        $limit = $request->get('limit') ?? 2;
        $offset = $request->get('offset') ?? 0;

        $pages = 1;

        if ($totalPlaylists > $limit) {
            $pages = $totalPlaylists / $limit;
            $pages = ceil($pages);
        }


        $options['limit'] = $limit;
        $options['offset'] = $offset;


        while ($offset < $pages) {
            $options['offset'] = $offset;
            // if its last page, recalculate limit
            if ($offset === $pages - 1) {
                $options['limit'] = $totalPlaylists - $offset * $limit;
            }
            $currentPlaylists = $getPlaylists($options)->items;

            foreach ($currentPlaylists as $playlist) {
                // playlist name = ATM Release Radar skip
//                if ($playlist->name === 'ATM Release Radar') {
//                    continue;
//                }
                $playlist = collect($playlist)->toArray();
                $spotifyService->processPlaylist($playlist, $spotifyService);
//                dump([
//                    'current' => $playlist['name'],
//                ]);
            }

            $playlists = $currentPlaylists;
            $playlists = array_merge($playlists, $currentPlaylists);
            $offset += 1;
        }

        $preparedPlaylists = $spotifyService->preparePlaylistsTable($playlists);
        // If the playlist is new, add it to the database

        return new JsonResponse([
            'status' => 'OK',
            'total' => $totalPlaylists,
            'playlist' => $preparedPlaylists,
        ]);
    }
}
