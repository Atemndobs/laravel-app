<?php

namespace App\Services\Spotify;

use App\Http\Controllers\Api\SpotifyAppController;
use App\Http\Controllers\Api\SpotifyAuthController;
use App\Models\SpotifyAuth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyAuthService
{
    public function auth()
    {
        $controller = app()->make(SpotifyAuthController::class);
        $controller->login();
        $authUrl = session('spotify_auth');

//        $spotify_auth = new SpotifyAuth();
//        $spotify_auth->auth_url = $authUrl;
//        $spotify_auth->save();
        return json_encode($authUrl, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}