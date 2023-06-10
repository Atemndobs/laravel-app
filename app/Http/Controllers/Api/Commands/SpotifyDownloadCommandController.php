<?php

namespace App\Http\Controllers\Api\Commands;

use App\Console\Commands\Scraper\SpotifyDownloadCommand;
use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Services\Storage\MinioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class SongController
 */
class SpotifyDownloadCommandController extends Controller
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
       // Artisan::call('song:import');
        // call song import command
        $commandName = $this->spotifyDownloadCommand->getName();
        Artisan::call("$commandName" , [
            'url' => $request->get('url')
        ]);

        $output = explode("\n", trim(Artisan::output()));
        $output = array_filter($output, function ($line) {
            return !empty($line);
        });

        // check if $output is contains "file already exists" message
        $isFileExists = false;
        foreach ($output as $line) {
            if (str_contains($line, '(file already exists)') || str_contains($line, 'Skipping')) {
                $isFileExists = true;
                break;
            }
        }

        // check if  $output contains "Skipping"
        $songLine = [];

        if ($isFileExists) {
            foreach ($output as $line) {
                if (str_contains($line, 'Skipping')) {
                    $songLine = explode('Skipping', $line);
                    // remove "(file already exists)" from $line
                    $songLine[1] = str_replace('(file already exists)', '', $songLine[1]);
                    $songLine = explode('...', $songLine[1]);
                    $songLine = array_map('trim', $songLine);
                    $slug = Str::slug($songLine[0], '_') ;
                    $filepath = "$slug.mp3";
                    $s3Url = "http://s3.atemkeng.de:9000/curator/music/$filepath";
                    $output['slug'] = $slug;
                    $output['title'] = $songLine[0];
                    $output['path'] = $s3Url;

                }
            }
            Artisan::call('song:import');
            Artisan::call('song:import');
            $importoutput = explode("\n", trim(Artisan::output()));
        }
        Log::info(json_encode(['FIle Exists' => $isFileExists], JSON_PRETTY_PRINT));

        return new JsonResponse([
            'message' => 'Song import command executed successfully',
            'data' => array_values($output),
        ], 200);

    }
}
