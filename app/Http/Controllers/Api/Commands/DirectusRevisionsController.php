<?php

namespace App\Http\Controllers\Api\Commands;

use App\Console\Commands\Db\DirectusRevisionsCommand;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DirectusRevisionsController extends Controller
{
    public Request $request;
    public DirectusRevisionsCommand $directusRevisionsCommand;

    /**
     * @param Request $request
     * @param DirectusRevisionsCommand $directusRevisionsCommand
     */
    public function __construct(
        Request $request,
        DirectusRevisionsCommand $directusRevisionsCommand
    ) {
        $this->request = $request;
        $this->directusRevisionsCommand = $directusRevisionsCommand;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function execute(Request $request): JsonResponse
    {
        Artisan::call($this->directusRevisionsCommand->getName());
        $output = explode("\n", trim(Artisan::output()));
        $output = array_filter($output, function ($line) {
            return !empty($line);
        });

        return new JsonResponse([
            'message' => 'Directus revisions command executed successfully',
            'data' => array_values($output)
        ], 200);
    }
}
