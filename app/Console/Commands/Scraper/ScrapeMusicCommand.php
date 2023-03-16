<?php

namespace App\Console\Commands\Scraper;

use App\Services\Scraper\Blogs\Zaplaylist;
use App\Services\Scraper\MusicBlogScraper;
use App\Services\Scraper\SoundcloudService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class ScrapeMusicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:song {site?} {--a|artist=null} {--p|playlist=null} {--t|title=null} {--m|mixtape=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape music from Chosen website ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // sources = fakaza, tooxclusive, hiphopkit, zaplaylist, soundcloud

        $scrapedMusic = [];
        $data = [];
        $site = $this->argument('site');
        $artist = $this->option('artist');
        $playlist = $this->option('playlist');
        $title = $this->option('title');

        $allOptions = $this->options();
        // filter out null values
        $allOptions = array_filter($allOptions, function ($arg) {
            return $arg !== null && $arg !== '' && $arg !== 'null' && $arg !== false;
        });
        if (count($allOptions) === 0) {
            $this->info('No arguments provided');

            return 0;
        }
        $musicScraper = new MusicBlogScraper();

        if ($site === 'zaplaylist') {
            $zaplalist = new Zaplaylist();

            $songLinks = $zaplalist->getSongsFromZaplaylist($allOptions);

            if (empty(array_filter($songLinks))) {
                $this->info('No songs found');

                return 0;
            }

            if (count($songLinks) === 1) {
                $link = Arr::first($songLinks);
                $answer = $this->ask("$link found,do you want to download it? (y/n)", 'n');
                if ($answer === 'n') {
                    $this->error('Download cancelled');

                    return 0;
                }
                // magenta line output
                $this->line("<fg=magenta> downloading song: .$link </>");
                $zaplalist->downloadZaplaylist($link);

                return 0;
            } else {
                // found multiple songs
                $this->info('Found '.count($songLinks).' songs');
                $answer = $this->ask('Do you want to download all songs? (y/n)', 'n');
                if ($answer === 'y') {
                    $this->downloadMany($songLinks);

                    return 0;
                }
                // magenta line output
                $this->line('<fg=blue> select songs to download: </>');
                $songLinks = array_values($songLinks);
                // start array key from 1 instead of 0
                $songLinks = array_combine(range(1, count($songLinks)), $songLinks);
                $songsToDownload = $this->choice(
                    'Choose songs to download, enter comma separated list',
                    $songLinks,
                    null,
                    null,
                    true
                );

                $extractedTitles = $this->extractMultipleTitles($songsToDownload);
                $count = count($extractedTitles);
                $this->comment("You selected: $count songs");
                foreach ($extractedTitles as $title => $link) {
                    $data[] = [$title, $link];
                }
                $this->table(['number', 'title'], $data);
                $this->confirm('Do you want to download these songs?', true);
                $this->downloadMany($songsToDownload);

                return 0;
            }
        }

        if ($site === 'hiphopkit') {
            $scrapedMusic = $musicScraper->getSongsFromHiphopkit($artist);
        }

        if ($site === 'tooxclusive') {
            $tooxclusive = $musicScraper->getMusicFromSource('https://tooxclusive.com/', 'main/download-mp3/');
            //  $scrapedMusic = $musicScraper->getSongsFromTooxclusive($allOptions);
        }
        if ($site === 'fakaza') {
            $fakaza = $musicScraper->getMusicFromSource('https://fakaza.com/', 'download-mp3/');
            // $scrapedMusic = $musicScraper->getSongsFromFakaza($allOptions);
        }

        if ($site === 'sc') {
            $soundCloudService = new  SoundcloudService();
            $choice_1 = "Get $artist PlayList";
            $choice_2 = "Get $artist Liked Songs";

            if ($artist !== null) {
                $choice = $this->choice('Download Option', [
                    1 => $choice_1,
                    2 => $choice_2,
                ],
                1
                );

                if ($choice === $choice_2) {
                    $soundCloudService->getLikedSongsByArtis($artist);
                }
                if ($choice === $choice_1) {
                    ray($choice)->blue();
                    $playlistOptions = $soundCloudService->getArtistPlaylists($artist);
                    if (count($playlistOptions) === 0) {
                        $this->info('No playlists found');

                        return 0;
                    }
                    $playlistChoice = $this->choice('Download Option',
                        $playlistOptions,
                        0
                    );
                    ray()->clearAll();
                    ray($playlistChoice);

                    return $soundCloudService->downloadPlaylist($playlistChoice);
                }
            }

            if ($playlist !== null) {
                $soundCloudService->getCuratedPlaylist($playlist);
            }
        } elseif ($site !== null) {
            $scrapedMusic = $musicScraper->getMusicFromSource("https://{$site}.com/", 'main/download-mp3/');
        }

        //  $scrapedMusic = array_merge($tooxclusive, $fakaza);

        $this->extracted($scrapedMusic, $site, $data);

        return 0;
    }

    /**
     * @param  array  $scrapedMusic
     * @param  array|string|null  $site
     * @param  array  $data
     * @return void
     */
    public function extracted(array $scrapedMusic, array|string|null $site, array $data): void
    {
        $headers = [
            'source',
            'link',
        ];

        if (count($scrapedMusic) > 0) {
            foreach ($scrapedMusic as $music) {
                $data[] = [
                    'source' => $site,
                    'link' => $music,
                ];
            }
        }

        $this->output->table($headers, $data);

        $total = count($scrapedMusic);
        $this->output->info("extracted $total songs");
        info("========================================= DONE_SCRAPPING $site =================================");
    }

    /**
     * @param  mixed  $songLink
     * @return string
     */
    public function extractSongTitleFromLink(mixed $songLink): string
    {
        // extract song tile from song link https://zaplaylist.com/2022/03/12/download-diamond-platnumz-fresh-ft-focalistic-costa-titch-pabi-cooper-mp3/
        $songTitle = explode('/', $songLink)[6];
        $songTitle = str_replace('-', ' ', $songTitle);
        // remove 'download' from song title
        $songTitle = str_replace('download', '', $songTitle);
        $songTitle = str_replace('mp3', '', $songTitle);
        $songTitle = ucwords($songTitle);

        return $songTitle;
    }

    /**
     * @param  array  $songLinks
     * @return void
     */
    public function downloadMany(array $songLinks): void
    {
        $this->info('Downloading all songs');
        $bar = $this->output->createProgressBar(count($songLinks));
        foreach ($songLinks as $songLink) {
            $songTitle = $this->extractSongTitleFromLink($songLink);
            $this->line("<fg=cyan> downloading song: .$songTitle </>");
            //$zaplalist->downloadZaplaylist($songLink);
            $bar->advance();
            $this->newLine();
        }
        $bar->finish();
    }

    private function extractMultipleTitles(array $songLinks)
    {
        foreach ($songLinks as $key => $songLink) {
            $songLinks[$key] = $this->extractSongTitleFromLink($songLink);
        }

        return $songLinks;
    }
}
