<?php

namespace App\Console\Commands\Indexer;

use Illuminate\Console\Command;
use MeiliSearch\Client;
use MeiliSearch\Endpoints\Indexes;

class MeilisearchReindexer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'indexer:reindex {--index=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset All Meili-search Indexes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $index = $this->option('index');
        if (!$index) {
            $this->info('Resetting all indexes');
            $this->setAll();
            return 0;
        }
        $this->info("Resetting index $index");
        try {
            $this->setIndex($index);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function setAll()
    {
        $meiliSearch = new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
        $indexes = $meiliSearch->stats()['indexes'];
        $reIndexes = array_keys($indexes);
        foreach ($reIndexes as $index) {
            try {
                $this->setIndex($index);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }

    private function setIndex(string $item)
    {
        $service = new \App\Services\Birdy\MeiliSearchService();
        $method = 'set' . ucfirst($item ) . 'Index';

        dump( [
            "Method: " => class_basename($method),
        ]);


        /** @var Indexes $index */
        $index =  $service->$method();

        $filterable = $index->getFilterableAttributes();
        $filterable[] = 'Attribute =>  getFilterableAttributes';
        $searchable = $index->getSearchableAttributes();
        $searchable[] = 'Attribute =>  getSearchableAttributes';
        $sortable = $index->getSortableAttributes();
        $sortable[] = 'Attribute =>  getSortableAttributes';
        $displayed = $index->getDisplayedAttributes();
        $displayed[] = 'Attribute =>  getDisplayedAttributes';
        $ranking = $index->getRankingRules();
        $ranking[] = 'Attribute =>  getRankingRules';
        $this->table(
            ["Attribute of the $item Index"],
            [
                array_reverse($filterable),
                array_reverse($searchable),
                array_reverse($sortable),
                array_reverse($displayed),
                array_reverse($ranking),

            ]
        );
    }

}
