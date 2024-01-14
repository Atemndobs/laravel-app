<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Base\SpotifyAuth;
use App\Models\User;
use Illuminate\Http\Request;
use SpotifyWebAPI\Session;

class SpotifyAuthController extends Controller
{
    /**
     * @var User
     */
    public User $user;

    public function __construct()
    {
        $this->user = (new User())->getLoggedInUser();
    }

    public function login()
    {
         $session = new Session(
            env('SPOTIFY_CLIENT_ID'),
            env('SPOTIFY_CLIENT_SECRET'),
            env('SPOTIFY_REDIRECT_URI')
        );

        $state = $session->generateState();
        $options = [
            'scope' => [
                'playlist-read-private',
                'user-read-private',
                'playlist-modify-public',
                'playlist-modify-private',
                'user-library-read',
            ],
            'state' => $state,
        ];

        return redirect()->away($session->getAuthorizeUrl($options));
    }

    public function callback(Request $request)
    {
        $session = new Session(
            env('SPOTIFY_CLIENT_ID'),
            env('SPOTIFY_CLIENT_SECRET'),
            env('SPOTIFY_REDIRECT_URI')
        );
        $session->requestAccessToken($request->input('code'));
        $accessToken = $session->getAccessToken();
        $refreshToken = $session->getRefreshToken();
        $expires = $session->getTokenExpiration();

        $spotifySession = [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires' => 1705248000,
        ];

        // Save access spotifySession to DB (spotify Auth table)
        $spotifyAuth = new SpotifyAuth();
        SpotifyAuth::query()->truncate();
        $spotifyAuth->access_token = $accessToken;
        $spotifyAuth->refresh_token = $refreshToken;
        $spotifyAuth->expires = $expires;
        $spotifyAuth->auth_url = json_encode($request->all());
        $spotifyAuth->save();

        $this->user->session = $spotifySession;
        $this->user->save();

        return redirect('/');
    }
}