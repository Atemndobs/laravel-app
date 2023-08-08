<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Release;
use App\Models\SpotifyAuth;
use App\Models\User;
use App\Services\Scraper\SpotifyMusicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyAppController extends Controller
{
    public SpotifyWebAPI $api;
    public User $user;

    public function __construct()
    {
        $this->user = (new User())->getLoggedInUser();
        $spotifySession  = json_decode($this->user->session, true);
        $accessToken = $spotifySession['access_token'];
        $this->api = new SpotifyWebAPI();
        $this->api->setAccessToken($accessToken);
    }

    public function index()
    {
        // save user spotify in session
        $user = $this->api->me();
        session(['spotify_user' => $user]);
        $redirectUri = env('SPOTIFY_LOGIN_REDIRECT_URI');
        return redirect()->to($redirectUri);
    }

    public function getPlaylists(Request $request)
    {
        $api = $this->api;
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
                if ($playlist->name === 'ATM Release Radar') {
                    continue;
                }
                $playlist = collect($playlist)->toArray();
                $spotifyService->processPlaylist($playlist, $spotifyService);
                $playlists[] = $playlist;
            }
            $offset += 1;
        }

        $preparedPlaylists = $spotifyService->preparePlaylistsTable($playlists);

        return new JsonResponse([
            'status' => 'OK',
            'total' => $totalPlaylists,
            'playlist' => $preparedPlaylists,
        ]);
    }
}
