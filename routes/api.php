<?php

use App\Websockets\SocketHandler\UpdateSongSocketHandler;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('health', HealthCheckJsonResultsController::class);

Orion::resource('songs', \App\Http\Controllers\Api\SongController::class);
Orion::resource('catalogs', \App\Http\Controllers\Api\CatalogController::class);
Orion::resource('files', \App\Http\Controllers\Api\FileController::class);

Route::get('/classify', [\App\Http\Controllers\Api\ClassificationController::class, 'classify']);
Route::get('/analyze/{track}', [\App\Http\Controllers\Api\ClassificationController::class, 'analyze']);


Route::get('classify/{slug}', [\App\Http\Controllers\Api\ClassificationController::class, 'findByTitle']);
Route::post('upload', [\App\Http\Controllers\Api\UploadController::class, 'upload']);

Route::post('songs/match', [\App\Http\Controllers\Api\MatchSongController::class, 'getSongMatch']);
Route::get('search/songs', [\App\Http\Controllers\Api\MeilesearchSongController::class, 'getSongs']);
Route::post('ping', [\App\Http\Controllers\Api\MeilesearchSongController::class, 'ping']);
//Route::get('songs/match/{title}/{$attribute}', [\App\Http\Controllers\Api\MatchSongController::class, 'matchByAttribute']);
Route::get('songs/search/{term}', [\App\Http\Controllers\Api\SongSearchController::class, 'searchSong']);
Route::get('songs/genre/{artist}', [\App\Http\Controllers\Api\SpotifyController::class, 'getArtistGenre']);
Route::get('spotify/search/{artist}', [\App\Http\Controllers\Api\SpotifyController::class, 'getSpotifySearch']);

WebSocketsRouter::webSocket('/socket/song', UpdateSongSocketHandler::class);
