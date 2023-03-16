<?php

namespace App\Console\Commands\Recommendation\Catalog;

use App\Services\Recommendation\CatalogService;
use Illuminate\Console\Command;

class exportCatalogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catalog:export {fileName?} ';

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
        $catalogService = new CatalogService();
        $fileName = $this->argument('fileName');
        // export catalog to csv using catalogService
        $catalog = $catalogService->exportCatalog("$fileName" ?? 'catalog.csv');
        $this->table(['id', 'item_id', 'item_name', 'item_category', 'description'], $catalog);

        return 0;
    }
}
