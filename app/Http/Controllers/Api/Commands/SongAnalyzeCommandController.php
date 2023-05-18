<?php

namespace App\Http\Controllers\Api\Commands;

use App\Console\Commands\Analysis\AnalyzeSongCommand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SongAnalyzeCommandController
{
    public Request $request;
    public AnalyzeSongCommand $analyzeSongCommand;

    /**
     * @param Request $request
     * @param AnalyzeSongCommand $analyzeSongCommand
     */
    public function __construct(
        Request $request,
        AnalyzeSongCommand $analyzeSongCommand
    ) {
        $this->request = $request;
        $this->analyzeSongCommand = $analyzeSongCommand;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function execute(Request $request): JsonResponse
    {
    Artisan::call($this->analyzeSongCommand->getName(), [
        'slug' => $request->get('slug')
    ]);
    $output = explode("\n", trim(Artisan::output()));
    $output = array_filter($output, function ($line) {
        return !empty($line);
    });

        return new JsonResponse([
            'message' => 'Song analyze command executed successfully',
            'data' => array_values($output)
        ], 200);

    }
}