<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use MeiliSearch\Http\Client;
use MeiliSearch\Contracts\DocumentsQuery;
use function example\int;

class MeilesearchSongController extends Controller
{
    public Client $client;

    public function __construct()
    {
        $this->client = new Client(env('MEILISEARCH_HOST'), env('MEILI_MASTER_KEY'));
    }

    public function getSongs()
    {
        $offset = request()->offset ?? 0;
        $limit = request()->limit ?? 10;

        $query =
            [
                'filter' => [
                    'analyzed = 1'
                ],
                'limit' => (int)$limit,
                'offset' => (int)$offset,
            ];

        Log::info(json_encode([
            'Method' => 'MeilesearchSongController@getSongs',
            'Position' => 'Before Try Catch',
            'query' => $query,
          //  'response' =>  $this->client->post('/indexes/songs/search', $query)
        ]));

        try {
            $search = $this->client->post('/indexes/songs/search', $query);
            Log::info(json_encode([
                'location' => 'MeilesearchSongController@getSongs',
                'response' => $search,
            ]));
        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        //unset($search['hits']);
        Log::info(json_encode([
            'method' => 'MeilesearchSongController@getSongs',
            'position' => 'After Try Catch',
            'RaW - response' => $search,
        ]));
        $search['total'] = $search['estimatedTotalHits'];
        unset($search['estimatedTotalHits']);
        unset($search['processingTimeMs']);
        unset($search['query']);
        unset($search['facetDistribution']);
        unset($search['hitsCount']);
        $search['offset'] = (int)$offset;
        $search['limit'] = (int)$limit;
        $searchTotal = $search['total'];
        $searchLast = $searchTotal / $limit;
        $search['last'] = (int)$searchLast;
        return response()->json($search);
    }

    public function ping()
    {
        $request = request()->all();
        info(json_encode($request));

        try {
        $status = $request['status'];
        if ($status == 'deleted') {
            return response()->json([
                'status' => 'delete notified',
            ]);
        }
        } catch (\Exception $e) {
            throw new \Exception('Process Deleted');
        }
        return response()->json([
            'status' => 'success',
        ]);
    }
}
