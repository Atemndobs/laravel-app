<?php

namespace App\Console\Commands\Recommendation\Catalog;

use App\Services\Recommendation\CatalogService;
use Illuminate\Console\Command;

class createUsageDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catalog:user';

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
        $catalogService->createUsageData();
        $this->info('Catalog usage data created');

        return 0;
    }
}
