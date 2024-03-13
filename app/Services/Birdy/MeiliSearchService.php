<?php

namespace App\Services\Birdy;

use App\Models\Catalog;
use App\Models\Finop;
use App\Models\Song;
use App\Services\Storage\MinioService;
use MeiliSearch\Client;
use MeiliSearch\Endpoints\Indexes;

class MeiliSearchService
{
    /**
     * @var Client
     */
    public Client $client;

    public function __construct()
    {
        $this->client = new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
    }

    /**
     * @return Indexes
     */
    public function setCatalogsIndex(): Indexes
    {
        $meiliSearch = $this->client;

        try {
            $meiliSearch->createIndex("catalogs");
            $meiliSearch->index("catalogs")->updateSearchableAttributes([
                'id',
                'item_name',
                'item_category',
                'description',
                'features_list',
            ]);
            $meiliSearch->index("catalogs")->updateFilterableAttributes([
                'id',
                'item_name',
                'item_category',
                'description',
                'features_list',
            ]);
            $meiliSearch->index("catalogs")->updateSortableAttributes([
                'id',
                'item_name',
                'item_category',

            ]);
            $meiliSearch->index("catalogs")->updateDisplayedAttributes([
                'id',
                'item_name',
                'item_category',
                'description',
                'features_list',
            ]);
        }catch (\Exception $e) {
            dump([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ]);
            throw new \Exception($e->getMessage());
        }
        $meiliSearch->index('catalogs')->update(['primaryKey' => 'id']);
        $meiliSearch->index('catalogs')->addDocuments(Catalog::all()->toArray());
        return $meiliSearch->index("catalogs");
    }

    /**
     * @return Indexes
     */
    public function setSongsIndex(): Indexes
    {
        $meiliSearch = $this->client;

        try {
            $meiliSearch->createIndex("songs");
            $meiliSearch->index("songs")->updateSearchableAttributes([
                'id', // 'id' is the primary key of the table
                "title",
                "author",
                "bpm",
                "key",
                "scale",
                "energy",
                "happy",
                "sad",
                "analyzed",
                "aggressiveness",
                "danceability",
                "relaxed",
                "played",
                "path",
                "slug",
                "image",
                "related_songs",
                "genre",
                "song_id"
            ]);
            $meiliSearch->index("songs")->updateFilterableAttributes([
                'id', // 'id' is the primary key of the table
                "title",
                "bpm",
                "key",
                "scale",
                "energy",
                "happy",
                "sad",
                "analyzed",
                "aggressiveness",
                "danceability",
                "relaxed",
                "slug",
                "status",
                "genre",
                "song_id"
            ]);
            $meiliSearch->index("songs")->updateSortableAttributes([
                'id', // 'id' is the primary key of the table
                "title",
                "bpm",
                "key",
                "scale",
                "energy",
                "happy",
                "sad",
                "analyzed",
                "aggressiveness",
                "danceability",
                "relaxed",
                "slug",
                "status",
                "genre",
                "song_id"
            ]);
            $meiliSearch->index("songs")->updateDisplayedAttributes([
                'id',
                "title",
                'author',
                "bpm",
                "key",
                "scale",
                "energy",
                "happy",
                "sad",
                "analyzed",
                "aggressiveness",
                "danceability",
                "relaxed",
                "played",
                "path",
                "slug",
                "image",
                "related_songs",
                'comment',
                'genre',
                'played',
                'status',
                'classification_properties',
                "song_id"
            ]);
            # Valid ranking rules are words, typo, sort, proximity, attribute, exactness and custom ranking rules.
            $meiliSearch->index("songs")->updateRankingRules([
                "typo",
                "words",
                "attribute",
                "sort",
                "proximity",
                "exactness"
            ]);
        }catch (\Exception $e) {
            dump([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ]);
            throw new \Exception($e->getMessage());
        }
        $meiliSearch->index('songs')->update(['primaryKey' => 'id']);
        $songs = Song::all()->toArray();
        $meiliSearch->index('songs')->addDocuments($songs);
        return $meiliSearch->index("songs");
    }

    /**
     * @return Indexes
     */
    public function setFinopsIndex(): Indexes
    {
        $meiliSearch = $this->client;

        try {
            // $meiliSearch->deleteIndex("finops");
            $meiliSearch->createIndex("finops");
            $meiliSearch->index("finops")->updateSearchableAttributes([
               // 'id', // 'id' is the primary key of the table
              //  'question_id',
                'question',
                'answer',
               // 'explanation',
            ]);
            $meiliSearch->index("finops")->updateFilterableAttributes([
                // 'id', // 'id' is the primary key of the table
                'question_id',
                //'question',
                //'answer',
                // 'explanation',
            ]);
            $meiliSearch->index("finops")->updateSortableAttributes([
                // 'id', // 'id' is the primary key of the table
                  'question_id',
//                'question',
//                'answer',
                // 'explanation',
            ]);
            $meiliSearch->index("finops")->updateDisplayedAttributes([
                //'id', // 'id' is the primary key of the table
                'question_id',
                'question',
                'answer',
                'explanation',
            ]);
            # Valid ranking rules are words, typo, sort, proximity, attribute, exactness and custom ranking rules.
            $meiliSearch->index("finops")->updateRankingRules([
                "typo",
                "words",
                "attribute",
                "sort",
                "proximity",
                "exactness"
            ]);
        }catch (\Exception $e) {
            dump([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
            ]);
            throw new \Exception($e->getMessage());
        }
        $meiliSearch->index('finops')->update(['primaryKey' => 'id']);
        $finops = Finop::all()->toArray();
        $meiliSearch->index('finops')->addDocuments($finops);
        return $meiliSearch->index("finops");
    }

    /**
     * @return Indexes
     */
    public function getSongIndex(): Indexes
    {
        return $this->client->index("songs");
    }
}
