<?php

namespace App\Console\Commands\Song;

use App\Services\SongSearchService;
use Illuminate\Console\Command;
use Meilisearch\Http\Client;

class SongSearchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:search {source?} {--s|site=} {--a|artist=} {--t|title=} {--o|offset=} {--l|limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // Search Song from Meilisearch
        $client = new Client(env('MEILISEARCH_HOST'), env('MEILI_MASTER_KEY'));
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


        try {
            $search = $client->post('/indexes/songs/search', $query);


        }catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }

        //unset($search['hits']);
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


        $hits = [];
        foreach ($search['hits'] as $hit) {
            $hits[] = [
                'id' => $hit['id'],
                'author' => $hit['author'],
                'title' => $hit['title'],
                'path' => $hit['path'],
            ];
        }
        // return search results in a table
        $this->table(['id', 'author', 'title', 'path'], $hits);



        // Search Song from Database
/*        $searchService = new SongSearchService();
        $source = $this->argument('source');
        $site = $this->option('site');
        $artist = $this->option('artist');
        $title = $this->option('title');

        if ($source === null || $source === 'db') {
            $res = $searchService->searchDb($artist, $title);
        }

        if ($source === 'web') {
            $res = $searchService->searchWebSite($site, $artist, $title);
        }*/


        return 0;
    }
}
