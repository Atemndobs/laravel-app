<?php

namespace App\Http\Controllers\Api\Commands;

use App\Console\Commands\Song\SongUpdateCommand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SongUpdateCommandController
{
    public Request $request;
    public SongUpdateCommand $songUpdateCommand;

    /**
     * @param Request $request
     * @param SongUpdateCommand $songUpdateCommand
     */
    public function __construct(
        Request $request,
        SongUpdateCommand $songUpdateCommand
    ) {
        $this->request = $request;
        $this->songUpdateCommand = $songUpdateCommand;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function execute(Request $request): JsonResponse
    {
    Artisan::call($this->songUpdateCommand->getName(), [
        'slug' => $request->get('slug')
    ]);
    $output = explode("\n", trim(Artisan::output()));
    $output = array_filter($output, function ($line) {
        return !empty($line);
    });

        return new JsonResponse([
            'message' => 'Song update properties command executed successfully',
            'data' => array_values($output)
        ], 200);

    }
}