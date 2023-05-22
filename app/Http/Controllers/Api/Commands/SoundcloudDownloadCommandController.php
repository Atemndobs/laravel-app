<?php

namespace App\Http\Controllers\Api\Commands;

use App\Console\Commands\Scraper\SoundcloudDownloadCommand;
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
    public SoundcloudDownloadCommand $soundcloudDownloadCommand;

    /**
     * @param Request $request
     * @param SoundcloudDownloadCommand $soundcloudDownloadCommand
     */
    public function __construct(
        Request $request,
        SoundcloudDownloadCommand $soundcloudDownloadCommand
    ) {
        $this->request = $request;
        $this->soundcloudDownloadCommand = $soundcloudDownloadCommand;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function execute(Request $request): JsonResponse
    {
        // call song import command
        Artisan::call($this->soundcloudDownloadCommand->getName() , [
            'link' => $request->get('url')
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
