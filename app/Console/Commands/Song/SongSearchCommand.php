<?php

namespace App\Console\Commands\Song;

use App\Services\SongSearchService;
use Illuminate\Console\Command;

class SongSearchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:search {source?} {--s|site=} {--a|artist=} {--t|title=}';

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
        $searchService = new SongSearchService();
        $source = $this->argument('source');
        $site = $this->option('site');
        $artist = $this->option('artist');
        $title = $this->option('title');

        if ($source === null || $source === 'db') {
            $res = $searchService->searchDb($artist, $title);
        }

        if ($source === 'web') {
            $res = $searchService->searchWebSite($site, $artist, $title);
        }

        dump($res);

        return 0;
    }
}
