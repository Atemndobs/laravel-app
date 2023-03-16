<?php

namespace App\Console\Commands\Recommendation\Catalog;

use App\Services\Recommendation\CatalogService;
use Illuminate\Console\Command;

class createCatalogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catalog:create {id?} ';

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
        ray()->clearAll();
        // convert all songs to catalogs
        $catalogService = new CatalogService();
        $id = $this->argument('id');

        if ($id === null) {
            $catalog = $catalogService->creatCatalog();
        } else {
            $catalog = [$catalogService->convertSongToCatalog($id)];
        }
        $this->info('Catalog created');
        $this->table(['id', 'item_name', 'item_category', 'description'], $catalog);

        return 0;
    }
}
