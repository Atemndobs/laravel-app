<?php

namespace App\Services\Song;

use Illuminate\Support\Facades\Log;
use Meilisearch\Http\Client;

class SearchSong
{
    public Client $client;
    public array $query;
    public function __construct()
    {
        $this->client = new Client(env('MEILISEARCH_HOST'), env('MEILI_MASTER_KEY'));
        $this->query = [];
    }

    public function getSongs($offset = 0, $limit = 10, $searchQueries = null)
    {
        $offset = request()->offset ?? $offset;
        $limit = request()->limit ?? $limit;
        $searchQueries = request()->filter ?? $searchQueries;
        // if searchQuery is not null then add it to the query
        if (!empty($searchQueries) && is_array($searchQueries)) {
//            foreach ($searchQueries as $searchQuery) {
//                $this->addQueryFilter($searchQuery['attribute'], $searchQuery['operator'], $searchQuery['value']);
//            }
            $this->query = $searchQueries;
        }


        // {"filter" : [
        //    {
        //        "attribute" : "title",
        //        "operator" : "=",
        //        "value" : "Kilometer"
        //    }
        //]
        //}

        $this->addQueryFilter('analyzed', '=',1);
        $query = $this->getQuery();

        //dd($query);
        Log::info(json_encode([
            'Method' => 'MeilesearchSongController@getSongs',
            'Position' => 'Before Try Catch',
            'query' => $query,
            //  'response' =>  $this->client->post('/indexes/songs/search', $query)
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        try {
            $search = $this->client->post('/indexes/songs/search', $query);
            Log::info(json_encode([
                'location' => 'MeilesearchSongController@getSongs',
                'response - estimatedTotalHits' => $search['estimatedTotalHits']
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        //unset($search['hits']);
        Log::info(json_encode([
            'method' => 'MeilesearchSongController@getSongs',
            'position' => 'After Try Catch',
            'RaW - estimatedTotalHits' => $search['estimatedTotalHits'],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
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
        return $search;
    }

    public function addQueryFilter(string $attribute, string $operator ,string $value): array
    {
        $this->query[] = $attribute . ' ' . $operator . ' ' . $value ;
        return $this->query;
    }

    public function getQuery()
    {
        $offset = request()->offset ?? 0;
        $limit = request()->limit ?? 10;

        $query =
            [
                'filter' => array_values($this->query),
                'limit' => (int)$limit,
                'offset' => (int)$offset,
            ];

        return $this->query = $query;
    }

}