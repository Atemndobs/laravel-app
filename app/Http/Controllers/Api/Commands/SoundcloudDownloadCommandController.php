<?php

namespace App\Http\Controllers\Api\Commands;

use App\Console\Commands\Scraper\SpotifyDownloadCommand;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * Class SongController
 */
class SoundcloudDownloadCommandController extends Controller
{
    public Request $request;
    public SpotifyDownloadCommand $spotifyDownloadCommand;

    /**
     * @param Request $request
     * @param SpotifyDownloadCommand $spotifyDownloadCommand
     */
    public function __construct(
        Request $request,
        SpotifyDownloadCommand $spotifyDownloadCommand
    ) {
        $this->request = $request;
        $this->spotifyDownloadCommand = $spotifyDownloadCommand;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function execute(Request $request): JsonResponse
    {
        // call song import command
        Artisan::call($this->spotifyDownloadCommand->getName() , [
            'url' => $request->get('url')
        ]);


        $output = explode("\n", trim(Artisan::output()));
        $output = array_filter($output, function ($line) {
            return !empty($line);
        });

        return new JsonResponse([
            'message' => 'Song import command executed successfully',
            'data' => array_values($output)
        ], 200);

    }
}
