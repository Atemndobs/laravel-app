<?php

namespace App\Console\Commands;

use App\Models\Song;
use App\Services\Birdy\MeiliSearchService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use MeiliSearch\Client;

class SearchIndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:reset {--i|index=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize / Index Meilsearch Entities  : Used for resetting Search Index';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $index = $this->option('index');
        if ($index === null) {
            $index = 'songs';
        }
        info("Resetting index $index");
        $meiliSearch = new Client(env('MEILISEARCH_HOST'), env('MEILISEARCH_KEY'));
        $meiliSearch->index($index)->deleteAllDocuments();
        if ($index === 'songs') {
            $meiliSearch->index($index)->addDocuments(Song::all()->toArray());
        }

        info('Updating BPMS');
        $this->call('song:bpm');
        info('=========================================BPMS_DONE==========================================');

        $this->call('song:status', ['--analyzed' => true, '--status' => true]);
        return 0;
    }
}
