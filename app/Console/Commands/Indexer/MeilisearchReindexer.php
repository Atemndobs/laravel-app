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
    protected $signature = 'indexer:reindex {--i|index=}';

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
        $this->setIndex($index);
    }

    private function setAll()
    {
        $meiliSearch = new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
        $indexes = $meiliSearch->stats()['indexes'];
        $reIndexes = array_keys($indexes);
        foreach ($reIndexes as $index) {
            $this->setIndex($index);
        }
    }

    private function setIndex(string $item)
    {
        if (in_array($item, ['files'])) {
            $this->error("Skipping index $item");
            return;
        }
        $service = new \App\Services\Birdy\MeiliSearchService();
        $method = 'set' . ucfirst($item ) . 'Index';
        /** @var Indexes $index */
        $index =  $service->$method();

        $filterable = $index->getFilterableAttributes();
        $searchable = $index->getSearchableAttributes();
        $sortable = $index->getSortableAttributes();
        $displayed = $index->getDisplayedAttributes();
        $ranking = $index->getRankingRules();
// Consolidate Attributes
        $attributes = [
            'Filterable' => $filterable,
            'Searchable' => $searchable,
            'Sortable' => $sortable,
            'Displayed' => $displayed,
            'Ranking' => $ranking,
        ];

// Initialize an array to hold the maximum number of attributes across all types
        $maxCounts = array_map('count', $attributes);
        $maxCount = max($maxCounts);

        $rows = [];

//Prepare Table Rows
        for ($i = 0; $i < $maxCount; $i++) {
            $row = [];
            foreach (['Filterable', 'Searchable', 'Sortable', 'Displayed', 'Ranking'] as $type) {
                // Check if the attribute exists, if not, add an empty string to keep the table aligned
                $row[$type] = $attributes[$type][$i] ?? '';
            }
            $rows[] = $row;
        }

// Generate Output
// Convert the rows into a format suitable for Laravel's table method
        $tableRows = [];
        foreach ($rows as $row) {
            $tableRow = [];
            foreach ($row as $column => $value) {
                // Remove the 'Attribute => ' part for display
                $value = str_replace('Attribute => ', '', $value);
                $tableRow[] = $value;
            }
            $tableRows[] = $tableRow;
        }

// Headers for the table
        $headers = ['Filterable', 'Searchable', 'Sortable', 'Displayed', 'Ranking'];

// Display the table
        $this->table($headers, $tableRows);

    }
}
