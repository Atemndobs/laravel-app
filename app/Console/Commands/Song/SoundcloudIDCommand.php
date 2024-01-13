<?php

namespace App\Console\Commands\Song;

use App\Services\Scraper\SoundcloudService;
use App\Services\Scraper\SpotifyMusicService;
use App\Services\Soundcloud\SoundCloudDownloadService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SoundcloudIDCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:sc-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Spotify ID for songs that do not have one.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // get songs that do not have a spotify id and that have source = spotify
        $songs = \App\Models\Song::query()->whereNull('song_id')
            ->orWhere('song_id', null)
            ->orWhere('song_id', '')
            ->where('source', '=', 'soundcloud')
            ->get();

        $soundCloudService = new SoundcloudService();

        // start progress bar
        $bar = $this->output->createProgressBar(count($songs));
        $songsUpdated = [];
        $stats = [
            'songs found' => count($songs),
        ];
        $this->warn(json_encode($stats, JSON_PRETTY_PRINT));

        /**
         * @var \App\Models\Song $song
         */
        foreach ($songs as $song) {
            // if author or title is null skip
            if (is_null($song->title) || is_null($song->author)) {
                $this->warn('Skipping song with null title or author');
                continue;
            }
            $this->warn('Searching for ' . $song->title . ' by ' . $song->author);
            $this->line('');

            try {
                $searchQuery = $song->title . ' ' . $song->author;
                $checkSource = $this->checkIfStringContainsSource($searchQuery);
                if ($checkSource != null && $checkSource != 'soundcloud') {
                    $this->warn('Source exist and is not soundcloud. Updating with source.');
                    $song->source = $checkSource;
                    $song->save();
                    continue;
                }
                $title_slug= Str::slug($song->title . ' ' . $song->author, '-');
                $author_slug= Str::slug($song->author, '-');
                $options = [
                    $title_slug,
                    $author_slug,
                ];

                $trackLink = $soundCloudService->getTrackLink($searchQuery, $options);
//                dump([
//                    'trackLink_1' => $trackLink,
//                    'searchQuery' => $searchQuery,
//                    'options' => $options,
//                ]);
                if (!$trackLink) {
                    $this->warn('Could not find track link for ' . $song->title . ' ' . $song->author);
                    $trackLink = $soundCloudService->getTrackLink($song->title . ' ' . $song->author);
//                    dump([
//                        'trackLink_2' => $trackLink,
//                        'searchQuery' => $song->title . ' ' . $song->author,
//                    ]);
                    if (!$trackLink) {
                        $trackLink = $soundCloudService->getTrackLink($song->slug);
//                        dump([
//                            'trackLink_3' => $trackLink,
//                            'searchQuery' => $song->slug,
//                        ]);
                    }
                }

                if ($trackLink == null) {
                    $this->warn('Could not find track link for ' . $song->title . ' ' . $song->author);
                    $song->source = null;
                    $song->save();
                    continue;
                }

                dd($trackLink);

                $songId = $soundCloudService->extractSoundcloudSongId($trackLink);
                //$author = $soundCloudService->extractAuthorFromTrackLink($trackLink);

                if ($songId) {
                    $song->song_id = $songId;
                    $song->song_url = $trackLink;
                    $song->save();
                    $songsUpdated[] = $songId;
                    $message = [
                        'found Song ID ' =>  $songId,
                        'song_url' => $trackLink,
                        'id' => $song->id,
                        'title' => $song->title,
                        'author' => $song->author,
                        'path' => $song->path,
                        'image' => $song->image,

                    ];
                    $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                }else{

                    dd($trackLink);
                    $this->error('Could not find song with title ' . $song->title . ' and artist ' . $song->author);
                    $song->played = true;
                    $song->source = null;
                    $song->save();
                }
            }catch (\Exception $e){
                $this->error($e->getMessage());
            }
            $this->line('');
            $bar->advance();
            $this->line('');
        }

        // finish progress bar
        $bar->finish();
        $message = [
            'message' => 'Finished searching for Spotify IDs. Total songs found: ' . count($songs) . '.',
            'songs found' => count($songs),
            'songs updated' => count($songsUpdated),
        ];
        $this->warn(json_encode($message, JSON_PRETTY_PRINT));
    }

    private function checkIfStringContainsSource(string $searchQuery)
    {
        $pattern = '/\b[\w-]+\.\w{2,}\b/';
        if (preg_match($pattern, $searchQuery, $matches)) {
            $domain = $matches[0];
            $this->line("<fg=yellow>Domain found: $domain</>");
            return $domain;
        } else {
            $this->line("<fg=red>No domain found</>");
            return null;
        }
    }
}
