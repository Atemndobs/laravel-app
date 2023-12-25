<?php

namespace App\Services\Birdy;

use App\Models\Catalog;
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
                "genre"
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
                "genre"
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
                'classification_properties'
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
//        $songsUpdated = [];
//        $minioService = new MinioService();
//        foreach ($songs as $song) {
//            $path = explode('/', $song['path']);
//            $file_name = end($path);
//            try {
//                $new_path = $minioService->getAudio($file_name);
//            }catch (\Exception $e) {
//                $songsUpdated[] = $song;
//                continue;
//            }
//            $song['path'] = $new_path;
//            $songsUpdated[] = $song;
//        }
        $meiliSearch->index('songs')->addDocuments($songs);
        return $meiliSearch->index("songs");
    }

    /**
     * @return Indexes
     */
    public function getSongIndex(): Indexes
    {
        return $this->client->index("songs");
    }
}
