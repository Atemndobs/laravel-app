<?php

namespace App\Console\Commands\Scraper;

use App\Services\Scraper\BandcampService;
use Illuminate\Console\Command;

class BandCampDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:bandcamp {url?} {--a|artist=} {--t|title=}';

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
      //  $link = "https://terakora.bandcamp.com/track/sad-girlz-are-homeless-w-dave-nunes-legend-of-k-r-remix";
        $service = new BandcampService();
        $songLinks = [];
        $url = $this->argument('url');
        $artist = $this->option('artist');
        if ($url) {
            $this->info("Scraping $url");
            $songLinks[] = $service->downloadSong($url);
        } elseif ($artist) {
            $this->info("Scraping $artist");
            $songLinks = $service->getSongLinksByArtisName($artist);
        } else {
            $this->error("No url or artist specified");
            return 1;
        }

        // progress bar
        $bar = $this->output->createProgressBar(count($songLinks));
        $bar->start();

        foreach ($songLinks as $songLink) {
            $this->info("Downloading $songLink");
            $dnload = $service->downloadSong($songLink);
            $this->info("Downloaded $dnload");
            $bar->advance();
        }
        $bar->finish();
        return 0;
    }
}
