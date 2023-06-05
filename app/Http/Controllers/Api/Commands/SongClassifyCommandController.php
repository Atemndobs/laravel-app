<?php

namespace App\Http\Controllers\Api\Commands;

use App\Console\Commands\Analysis\ClassifySongsCommand;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\JsonResponse;
class SongClassifyCommandController extends Controller
{
    public Request $request;
    public  ClassifySongsCommand $classifySongsCommand;

    /**
     * @param Request $request
     * @param AnalyzeSongCommand $analyzeSongCommand
     */
    public function __construct(
        Request $request,
        ClassifySongsCommand $classifySongsCommand
    ) {
        $this->request = $request;
        $this->classifySongsCommand = $classifySongsCommand;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function execute(Request $request): JsonResponse
    {
        Artisan::call($this->classifySongsCommand->getName());
        $output = explode("\n", trim(Artisan::output()));
        $output = array_filter($output, function ($line) {
            return !empty($line);
        });

        return new JsonResponse([
            'message' => 'Song classify command executed successfully',
            'data' => array_values($output)
        ], 200);

    }
}
