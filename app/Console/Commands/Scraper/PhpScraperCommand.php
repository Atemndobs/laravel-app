<?php

namespace App\Console\Commands\Scraper;

use App\Services\Phpscraper;
use Illuminate\Console\Command;

class PhpScraperCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:php {url} {--a|all=} {--t|title=} {--i|images=} {--s|assets=}';

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
        $url = $this->argument('url');
        $images = $this->option('images');
        $title = $this->option('title');
        $assets = $this->option('assets');
        $all = $this->option('all');

        $options = [
            'url' => $url,
            'images' => $images,
            'all' =>  $all,
            'title' => $title,
            'assets' => $assets,
        ];

        dump($options);

        $scraper = new Phpscraper();
        dd($scraper->scrape($url, $options));
    }
}
