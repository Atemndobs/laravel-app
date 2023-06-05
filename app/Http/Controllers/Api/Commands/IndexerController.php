<?php

namespace App\Http\Controllers\Api\Commands;

use App\Console\Commands\Indexer\MeilisearchReindexer;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class IndexerController extends Controller
{
    public Request $request;
    public MeilisearchReindexer $meilisearchReindexer;

    /**
     * @param Request $request
     * @param MeilisearchReindexer $meilisearchReindexer
     */
    public function __construct(
        Request $request,
        MeilisearchReindexer $meilisearchReindexer
    ) {
        $this->request = $request;
        $this->meilisearchReindexer = $meilisearchReindexer;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function execute(Request $request): JsonResponse
    {
        $indices = $request->get('indices');
        if (!$indices) {
            return new JsonResponse([
                'message' => 'No indices provided',
                'data' => []
            ], 400);
        }
        // explode indices
        $indices = explode(',', $indices);
        $outputs = [];

        // sail artisan scout:import 'App\Models\Song' && sail artisan scout:index songs

        foreach ($indices as $index) {
            $model = "App\Models\\" . ucfirst($index);
            Artisan::call("scout:import", [
                'model' => $model
            ]);
            $output = explode("\n", trim(Artisan::output()));
            $outputs[] = array_filter($output, function ($line) {
                return !empty($line);
            });
        }

        Artisan::call($this->meilisearchReindexer->getName());
        $output1 = explode("\n", trim(Artisan::output()));
        $outputs[] = array_filter($output1, function ($line) {
            return !empty($line);
        });


        return new JsonResponse([
            'message' => 'Song analyze command executed successfully',
            'data' => array_values($outputs)
        ], 200);

    }
}
