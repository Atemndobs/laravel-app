<?php

namespace App\Console\Commands\Scraper;

use Illuminate\Console\Command;

class BlogMusicDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:blog {site?} {--a|artist=null} {--p|playlist=null} {--t|title=null} {--m|mixtape=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download Curated Music From Blogs like Clacified';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->argument('site') === 'null') {
            $this->info('Downloading latest music from all blogs');
            return 0;
        }
        $site = $this->argument('site');
        $artist = $this->option('artist');

        dd($this->options());
        return 0;
    }
}
