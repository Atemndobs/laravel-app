<?php

namespace App\Console\Commands\Scraper;

use App\Models\Song;
use App\Services\Scraper\SoundcloudService;
use App\Services\Soundcloud\SoundCloudDownloadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SoundcloudDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:sc {--l|link=null} {--a|artist=null} {--p|playlist=null} {--t|title=null} {--m|mixtape=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download music from Soundcloud by Link, artist name tiles or playlist';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $link = $this->option('link');
        $artist = $this->option('artist');
        $title = $this->option('title');
        $playlist = $this->option('playlist');
        $mixtape = $this->option('mixtape');

        if ((string)$link !== 'null') {
            $this->info('Downloading from link: ' . $link);
            $option = '-l';
            $url = $link;
        }
        if ($artist !== 'null') {
            $this->info('Downloading from artist: ' . $artist);
            $option = '-a';
            $url = $artist;
        }
        if ($title !== 'null') {
            $this->info('Downloading from title: ' . $title);
            $option = '-t';
            $url = $title;
        }
        if ($playlist !== 'null') {
            $this->info('Downloading from playlist: ' . $playlist);
            $option = '-p';
            $url = $playlist;
        }
        if ($mixtape !== 'null') {
            $this->info('Downloading from mixtape: ' . $mixtape);
            $option = '-m';
            $url = $mixtape;
        }
        $soundcloudService = new SoundCloudDownloadService();
        $downloadLinks = $soundcloudService->prepareSongLinksFromFile()['links'];
        $this->warn("Found Soundcloud links: " . count($downloadLinks));
        // Start progress bar
        $bar = $this->output->createProgressBar(count($downloadLinks));
        // Start recording time
        $startTime = microtime(true);
        // calculate estimated time as 30 seconds per song
        $estimatedTime = (count($downloadLinks) * 12) / 60 . " mins";
        $this->line("<fg=bright-magenta>Estimated time: $estimatedTime</>");
        foreach ($downloadLinks as $downloadLink) {
            $bar->advance();
            $this->line('');
            try {
                $filename = $this->extractTrackNameFromLink($downloadLink);
                $soundcloudSongId = $this->extractSoundCloudSongId($downloadLink); // Function to extract the ID
                // create the path to the song
                $path = '/var/www/html/storage/app/public/uploads/audio/soundcloud/' . $soundcloudSongId;
                // make sure the directory exists if not create it
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $downloadPath = '/var/www/html/storage/app/public/uploads/audio/soundcloud/' . $soundcloudSongId;
                // output the shell command to the console
                $shell = shell_exec("scdl -l $downloadLink --path $downloadPath  2>&1");
              //  dump(shell_exec("ls -la /var/www/html/storage/app/public/uploads/audio/soundcloud/$soundcloudSongId"));
                // get the file and get its name
                $file = glob("/var/www/html/storage/app/public/uploads/audio/soundcloud/$soundcloudSongId/*.mp3");
                $fileName = basename($file[0]);
                $fileName = str_replace('.mp3', '', $fileName);
                $slug = Str::slug($fileName, '_');

                // here is the shell output pattern below, From th 3rd line (pattern Author - Track, Extract the track name and the author
                // Soundcloud Downloader
                //Found a track
                //Downloading 1da Banton - African Woman
                //Setting tags...
                //1da Banton - African Woman.mp3 Downloaded.

                dump($shell);
                $shellOutput = explode("\n", $shell);
                dump([
                    'shellOutput' => $shellOutput,
                    'count' => count($shellOutput),
                ]);
                $shellOutput = array_filter($shellOutput);
                $shellOutput = array_values($shellOutput);
                $trackName = $shellOutput[2];
                $trackName = str_replace('Downloading ', '', $trackName);
                $trackName = str_replace('.mp3 Downloaded.', '', $trackName);


                $author = explode(' - ', $trackName)[0];
                $title = explode(' - ', $trackName)[1];
                $title = str_replace('_', ' ', $title);

                $song_url = $downloadLink;
                $song_id = $soundcloudSongId;
                $filepath = $path . '/' . $slug . '.mp3';
                $message = [
                    'downloadLink' => $downloadLink,
                    'filename' => $filename,
                    'soundcloudSongId' => $soundcloudSongId,
                    'downloadPath' => $downloadPath,
                    'elapsed_time_secs' => (microtime(true) - $startTime)  . ' secs', // in 2 dp
                    'elapsed_time' => (microtime(true) - $startTime) / 60 . ' mins',
                    'title' => $title,
                    'author' => $author,
                    'song_url' => $song_url,
                    'song_id' => $song_id,
                    'slug' => $slug,
                    'filepath' => $filepath,
                    'source' => 'soundcloud',
                ];

                Log::info(json_encode($message, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                $this->info(json_encode($message, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

                //  save new song to DB
                $song = new \App\Models\Song();
                $song->title = $title;
                $song->author = $author;
                $song->song_url = $song_url;
                $song->song_id = $song_id;
                $song->slug = $slug;
                $song->path = $filepath;
                $song->source = "spotify";
                try {
                    $song->save();
                    $this->info('Song with ID ' . $song_id . ' has been saved to DB.');
                } catch (\Exception $e) {
                    $error = [
                        'error' => "Song Already Exists in DB",
                        'message' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                    ];
                    Log::warning(json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                }

                $this->call('song:import', [
                    '--path' => "/var/www/html/storage/app/public/uploads/audio/$slug.mp3",
                ]);
            }catch (\Exception $e) {
                $this->error($e->getMessage());
                dd($e->getMessage());
            }
            $souncdlInfo = [
                'downloaded_songs' => count($downloadLinks),
                'elapsed_time' => microtime(true) - $startTime / 60 . ' mins',
                'originally_estimated_time' => $estimatedTime,
            ];
            Log::info(json_encode($souncdlInfo, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            $this->line("<fg=bright-cyan>" . json_encode($souncdlInfo, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "</>");

        }
        $bar->finish();
        $this->line('');
        $this->info('Download completed');
        $completeMessage = [
            'downloaded_songs' => count($downloadLinks),
            'elapsed_time' => microtime(true) - $startTime / 60 . ' mins',
            'originally_estimated_time' => $estimatedTime,
        ];
        Log::info(json_encode($completeMessage, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        $this->line("<fg=bright-cyan>" . json_encode($completeMessage, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "</>");
        return 0;
    }

    private function extractSoundCloudSongId($url)
    {
        // Use parse_url to get the path component of the URL
        $path = parse_url($url, PHP_URL_PATH);

        // Remove the leading slash
        $path = ltrim($path, '/');

        // Replace slashes with underscores or another separator
        // This creates a unique identifier based on the username and song title
        $id = str_replace('/', '_', $path);

        return $id;
    }

    public function extractTrackNameFromLink(string $link)
    {
        $link = explode('/', $link);
        $n = count($link);
        $trackName = '';
        foreach ($link as $i => $iValue) {
            if ($i === $n - 1) {
                $trackName = $iValue;
            }
        }
        return $trackName;
    }

    /**
     * @param array|string $slug
     * @param string $songPath
     * @param string $webPath
     * @param bool|array|string|null $link
     * @return void
     */
    public function prepareOutputMessage(array|string $slug, string $songPath, string $webPath, bool|array|string|null $link): void
    {
        $song = Song::query()->where('slug', $slug)->first();
        $message = [
            'slug' => $slug,
            'song_path' => $songPath,
            'webPath' => $webPath,
            'link' => $link,
            'artist' => $song->artist,
            'title' => $song->title,
            'path' => $song->path,
        ];
        $this->info(json_encode($message, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        Log::info(json_encode(['song_data' => $message], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

}
