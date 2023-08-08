<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SpotifyWebAPI\Session;

class SpotifyAuthController extends Controller
{
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
            ],
            'state' => $state,
        ];
        session(['spotify_state' => $state]);
        return redirect()->to($session->getAuthorizeUrl($options));
    }

    public function callback(Request $request)
    {
        $session = new Session(
            env('SPOTIFY_CLIENT_ID'),
            env('SPOTIFY_CLIENT_SECRET'),
            env('SPOTIFY_REDIRECT_URI')
        );

        $state = $request->query('state');
        $storedState = session('spotify_state');

        if ($state !== $storedState) {
            return new \Exception('Invalid state');
        }

        $session->requestAccessToken($request->query('code'));
        session([
            'spotify_access_token' => $session->getAccessToken(),
            'spotify_refresh_token' => $session->getRefreshToken(),
        ]);
        return redirect()->route('spotify.logged_in');
    }
}
