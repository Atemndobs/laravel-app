<?php

namespace App\Http\Controllers\Api\Commands;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * Class SongController
 */
class SongImportCommandController extends Controller
{
    public Request $request;

    /**
     * @param Request $request
     */
    public function __construct(
        Request $request,
    ) {
        $this->request = $request;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function execute(Request $request): JsonResponse
    {
        // call song import command
        Artisan::call('song:import');
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
