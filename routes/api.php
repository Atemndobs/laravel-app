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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    // if no user is logged in, $request->user() should return the first admin user or user with admin role
//
//    return $request->user();
//});

Route::middleware('auth:sanctum')->group(function () {
    // Protected routes that require authentication

    // Example route
    Route::get('/user', function (Request $request) {
        return $request->user();
    });    // Example route

});


Route::get('health', HealthCheckJsonResultsController::class);

Orion::resource('songs', \App\Http\Controllers\Api\SongController::class);
Orion::resource('catalogs', \App\Http\Controllers\Api\CatalogController::class);
Orion::resource('files', \App\Http\Controllers\Api\FileController::class);

Route::get('/classify', [\App\Http\Controllers\Api\ClassificationController::class, 'classify']);
Route::get('/analyze/{track}', [\App\Http\Controllers\Api\ClassificationController::class, 'analyze']);


Route::get('classify/{slug}', [\App\Http\Controllers\Api\ClassificationController::class, 'findByTitle']);
Route::post('upload', [\App\Http\Controllers\Api\UploadController::class, 'upload']);
Route::post('songs/upload', [\App\Http\Controllers\Api\SongUploadController::class, 'uploadSong']);

Route::post('songs/match', [\App\Http\Controllers\Api\MatchSongController::class, 'getSongMatch']);
Route::get('songs/match/criteria/get', [\App\Http\Controllers\Api\MatchCriteriaController::class, 'getCriteria']);
Route::post('songs/match/criteria/set', [\App\Http\Controllers\Api\MatchCriteriaController::class, 'setCriteria']);
// cleat match criteria
Route::post('songs/match/criteria/clear', [\App\Http\Controllers\Api\MatchCriteriaController::class, 'clearCriteria']);
Route::get('search/songs', [\App\Http\Controllers\Api\MeilesearchSongController::class, 'getSongs']);
Route::post('ping', [\App\Http\Controllers\Api\MeilesearchSongController::class, 'ping']);
//Route::get('songs/match/{title}/{$attribute}', [\App\Http\Controllers\Api\MatchSongController::class, 'matchByAttribute']);
Route::get('songs/search/{term}', [\App\Http\Controllers\Api\SongSearchController::class, 'searchSong']);
Route::get('songs/genre/{artist}', [\App\Http\Controllers\Api\SpotifyController::class, 'getArtistGenre']);
Route::get('spotify/search/{artist}', [\App\Http\Controllers\Api\SpotifyController::class, 'getSpotifySearch']);

// command controller routes
Route::post('commands/song/import', [\App\Http\Controllers\Api\Commands\SongImportCommandController::class, 'execute']);
Route::post('commands/song/analyze', [\App\Http\Controllers\Api\Commands\SongAnalyzeCommandController::class, 'execute']);
Route::post('commands/song/classify', [\App\Http\Controllers\Api\Commands\SongClassifyCommandController::class, 'execute']);
Route::post('commands/index', [\App\Http\Controllers\Api\Commands\IndexerController::class, 'execute']);

Route::post('commands/song/update', [\App\Http\Controllers\Api\Commands\SongUpdateCommandController::class, 'execute']);
Route::post('commands/song/download/spotify', [\App\Http\Controllers\Api\Commands\SpotifyDownloadCommandController::class, 'execute']);
Route::post('commands/song/download/sc', [\App\Http\Controllers\Api\Commands\SoundcloudDownloadCommandController::class, 'execute']);
Route::get('commands/backup/run', [\App\Http\Controllers\Api\Storage\DatabaseBackupStorageController::class, 'runBackup']);
Route::get('commands/directus/clear_revisions', [\App\Http\Controllers\Api\Commands\DirectusRevisionsController::class, 'execute']);
// download backup
Route::get('storage/backup/download', [\App\Http\Controllers\Api\Storage\DatabaseBackupStorageController::class, 'downloadBackup']);
Route::get('storage/access', [\App\Http\Controllers\Api\Storage\FileAccessRefreshController::class, 'execute']);

// upload backup
Route::post('storage/backup/upload', [\App\Http\Controllers\Api\Storage\DatabaseBackupStorageController::class, 'uploadBackup']);
Route::get('storage/backup/store', [\App\Http\Controllers\Api\Storage\DatabaseBackupStorageController::class, 'storeBackup']);
Route::get('/spotify/app', [\App\Http\Controllers\Api\SpotifyAppController::class, 'index']);
Route::get('/spotify/auth', [\App\Http\Controllers\Api\SpotifyAuthController::class,'login']);
Route::get('/spotify/callback', [\App\Http\Controllers\Api\SpotifyAuthController::class, 'callback']);

// Route for songKeyscontroller
Route::get('songkeys', [\App\Http\Controllers\Api\SongKeyController::class, 'index']);
Route::get('song/keys', [\App\Http\Controllers\Api\SongKeyController::class, 'getSongKeys']);

// Routes for songGenreController
Route::get('songgenres', [\App\Http\Controllers\Api\SongGenreController::class, 'index']);
Route::get('song/genres', [\App\Http\Controllers\Api\SongGenreController::class, 'getSongGenres']);

// webhooks
Route::webhooks('/webhooks/songs');

// websockets
WebSocketsRouter::webSocket('/socket/song', UpdateSongSocketHandler::class);
