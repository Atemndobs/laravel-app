<?php

namespace App\Console\Commands\Scraper;

use App\Models\Song;
use App\Services\Scraper\SoundcloudService;
use App\Services\Soundcloud\SoundCloudDownloadService;
use App\Services\Storage\AwsS3Service;
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
        $missingSongs = [];
        foreach ($downloadLinks as $downloadLink) {
            $bar->advance();
            $this->line('');
            try {
                $filename = $this->extractTrackNameFromLink($downloadLink);
                $soundcloudSongId = $this->extractSoundCloudSongId($downloadLink); // Function to extract the ID
                // check if song exists in DB by song_id
                $songExists = \App\Models\Song::where('song_id', $soundcloudSongId)->first();
                if ($songExists) {
                    // check path , if path starts with /var/html/www/ then change it to the s3 oath
                    dump($songExists->path);
                    if (strpos($songExists->path, '/var/www/html/') !== false) {
                        $songExists->path = "https://curators3.s3.amazonaws.com/music/" . basename($songExists->path);
                        $songExists->save();
                    }
                    $this->error('Song with ID ' . $soundcloudSongId . ' already exists in DB.');
                    $message = [
                        'songExists' => [
                            'song_id' => $songExists->song_id,
                            'id' => $songExists->id,
                            'title' => $songExists->title,
                            'author' => $songExists->author,
                            'genre' => $songExists->genre,
                            'path' => $songExists->path,
                            'image' => $songExists->image,
                        ]
                    ];
                    Log::warning(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                    continue;
                }
                // create the path to the song
                $path = '/var/www/html/storage/app/public/uploads/audio/soundcloud/' . $soundcloudSongId;
                // make sure the directory exists if not create it
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $downloadPath = '/var/www/html/storage/app/public/uploads/audio/soundcloud/' . $soundcloudSongId;
                $shell = shell_exec("scdl -l $downloadLink --path $downloadPath  2>&1");
                $file = glob("/var/www/html/storage/app/public/uploads/audio/soundcloud/$soundcloudSongId/*.mp3");
                $fileName = basename($file[0]);
                $fileName = str_replace('.mp3', '', $fileName);
                $slug = Str::slug($fileName, '_');

                $shellOutput = explode("\n", $shell);
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

                $s3Path = "https://curators3.s3.amazonaws.com/music/$slug.mp3";
                //  save new song to DB
                $song = new \App\Models\Song();
                $song->title = $title;
                $song->author = $author;
                $song->song_url = $song_url;
                $song->song_id = $song_id;
                $song->slug = $slug;
                $song->path = $s3Path;
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
                    $this->warn(json_encode([
                        'error' => "Song Already Exists in DB",
                        'message' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                }

                $this->call('song:import', [
                    '--path' => "/var/www/html/storage/app/public/uploads/audio/$slug.mp3",
                ]);
            }catch (\Exception $e) {
                $this->error($e->getMessage());
                $missingSongs[] = $downloadLink;
            }
            $souncdlInfo = [
                'downloaded_songs' => count($downloadLinks),
                'elapsed_time' => microtime(true) - $startTime / 60 . ' mins',
                'songs_left' => count($downloadLinks) - count($downloadLinks),
                'estimated_time_left' => (count($downloadLinks) - count($downloadLinks)) * 12 / 60 . ' mins', // 12 seconds per song
                's3_path' => $s3Path,
                'originally_estimated_time' => $estimatedTime,
            ];
            Log::info(json_encode($souncdlInfo, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            $this->line("<fg=bright-cyan>" . json_encode($souncdlInfo, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "</>");

        }
        $bar->finish();
        $this->line('');
        $this->info('Download completed');

        // put missing songs in txt name missing_soundCloud_songs.txt (each son in a new line) and sore in s3
        $missingSongs = array_unique($missingSongs);
        $missingSongs = array_values($missingSongs);
        // create file missing_soundCloud_songs.txt
        $missingSongsFile = fopen("missing_soundCloud_songs.txt", "w");
        foreach ($missingSongs as $missingSong) {
            fwrite($missingSongsFile, $missingSong . "\n");
        }
        fclose($missingSongsFile);
        // upload file to s3
        $awsService = new AwsS3Service();
        $awsService->putObject("missing_soundCloud_songs.txt", "assets");

        $completeMessage = [
            'downloaded_songs' => count($downloadLinks),
            'elapsed_time' => microtime(true) - $startTime / 60 . ' mins',
            'originally_estimated_time' => $estimatedTime,
            'missing_songs' => count($missingSongs),
            'missing_songs_file' => "https://curators3.s3.amazonaws.com/assets/missing_soundCloud_songs.txt",
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
