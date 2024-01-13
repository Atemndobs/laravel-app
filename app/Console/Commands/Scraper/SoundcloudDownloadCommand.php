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
    protected $signature = 'scrape:sc {--l|link=null} {--a|artist=null} {--p|playlist=null} {--t|title=null} 
    {--m|mixtape=null} {--f|file=null} {--c|continue=null}';

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
        $file = $this->option('file');
        $continue = $this->option('continue');

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
        if ($file !== 'null') {
            $this->info('Downloading from file: ' . $file);
            $option = '-f';
            $url = $file;
        }
        if ($continue !== 'null') {
            $this->info('Downloading from continue: ' . $continue);
            $this->forceDownload($link);
            return 0;
        }
        $soundcloudService = new SoundCloudDownloadService();

        $downloadLinks = [];
        if ($link !== 'null') {
            $downloadLinks[] = $link;
        } elseif ($file !== 'null') {
            $downloadLinks = $soundcloudService->prepareSongLinksFromFile($file)['links'];
        } else {
            $downloadLinks = $soundcloudService->prepareSongLinksFromFile()['links'];
        }

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
                $this->processDownloadLink($downloadLink, $startTime);
//                $this->call('song:import', [
//                    '--path' => "/var/www/html/storage/app/public/uploads/audio/$slug.mp3",
//                ]);
            } catch (\Exception $e) {
                $sleepTime = 5;
                $error = [
                    'error' => "Download Failed",
                    'link' => $downloadLink,
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'method' => "processDownloadLink",
                ];
                Log::channel('soundcloud')->warning(json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                $this->line("<fg=red>" . json_encode($error, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "</>");
                // sleep for 20 seconds
                $missingSongs[] = $downloadLink;
                //save missing songs in a file
                $missingSongsFile = "missing_soundCloud_songs.txt";
                file_put_contents($missingSongsFile, implode("\n", $missingSongs));
                $this->line("<fg=cyan>We save this for next retry:  $downloadLink </>");
                $this->line("<fg=cyan> == Sleep $sleepTime seconds and continue == </>");
                // sleep($sleepTime);
            }
            $souncdlInfo = [
                'downloaded_songs' => count($downloadLinks),
                'elapsed_time' => (microtime(true) - $startTime) / 60 . ' mins',
                'originally_estimated_time' => $estimatedTime,
            ];
            Log::channel('soundcloud')->info(json_encode($souncdlInfo, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
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
            'elapsed_time' => (microtime(true) - $startTime) / 60 . ' mins',
            'originally_estimated_time' => $estimatedTime,
            'missing_songs' => count($missingSongs),
            'missing_songs_file' => "https://curators3.s3.amazonaws.com/assets/missing_soundCloud_songs.txt",
        ];
        Log::channel('soundcloud')->info(json_encode($completeMessage, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        $this->line("<fg=bright-cyan>" . json_encode($completeMessage, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "</>");
        return 0;
    }

    /**
     * Process the download link and save the song to the database.
     *
     * @param string $downloadLink The download link of the song.
     * @return int
     */
    private function processDownloadLink(string $downloadLink, $startTime)
    {
        $filename = $this->extractTrackNameFromLink($downloadLink);
        $soundcloudSongId = $this->extractSoundcloudSongId($downloadLink);
        $author = $this->extractAuthorFromLink($downloadLink);
        /** @var  Song $songExists */
        $songExists = $this->checkSongExist($soundcloudSongId);
        if ($songExists) {
            $songExists->author = $author;
            $songExists->source = 'soundcloud';
            $songExists->save();
            $this->error('Song with ID ' . $soundcloudSongId . ' already exists in DB. - Source Updated');
            $this->songExistError($songExists, $downloadLink);
            return 0;
        }

        $path = '/var/www/html/storage/app/public/uploads/audio/soundcloud/' . $soundcloudSongId;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $downloadPath = '/var/www/html/storage/app/public/uploads/audio/soundcloud/' . $soundcloudSongId;
        $shell = shell_exec("scdl -l $downloadLink --path $downloadPath  2>&1");
        $file = glob("/var/www/html/storage/app/public/uploads/audio/soundcloud/$soundcloudSongId/*.mp3");
        $fileName = $this->getFileName($file, $shell);
        $fileName = str_replace('.mp3', '', $fileName);
        $slug = Str::slug($fileName, '_');

        // check if slug exists in DB
        /** @var  Song $songExists */
        $songExists = Song::query()->where('slug', $slug)->first();
        if ($songExists) {
            $songExists->song_id ??= $soundcloudSongId;
            $songExists->save();
            $this->error('Song with Slug ' . $slug . ' already exists in DB. - song ID updated:' . $songExists->song_id);
            $this->songExistError($songExists, $downloadLink);
            return 0;
        }

        $trackName = $this->extractTrackInfoFromShellOutput($shell);
        $title = explode(' - ', $trackName)[0];
        $song_url = $downloadLink;
        $song_id = $soundcloudSongId;
        $filepath = $path . '/' . $slug . '.mp3';
        $message = [
            'downloadLink' => $downloadLink,
            'filename' => $filename,
            'soundcloudSongId' => $soundcloudSongId,
            'downloadPath' => $downloadPath,
            'elapsed_time_secs' => (microtime(true) - $startTime) . ' secs', // in 2 dp
            'elapsed_time' => (microtime(true) - $startTime) / 60 . ' mins',
            'title' => $title,
            'author' => $author,
            'song_url' => $song_url,
            'song_id' => $song_id,
            'slug' => $slug,
            'filepath' => $filepath,
            'source' => 'soundcloud',
        ];

        Log::channel('soundcloud')->info(json_encode($message, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        $this->info(json_encode($message, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        $s3Path = "https://curators3.s3.amazonaws.com/music/$slug.mp3";
        $song = new Song();
        $song->title = $title;
        $song->author = $author;
        $song->song_url = $song_url;
        $song->song_id = $song_id;
        $song->slug = $slug;
        $song->path = $s3Path;
        $song->source = "soundcloud";
        try {

            $song->save();
            $this->info('Song with ID ' . $song_id . ' has been saved to DB.');
        } catch (\Exception $e) {
            $error = [
                'error' => "Song Already Exists in DB",
                'link' => $downloadLink,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ];
            Log::channel('soundcloud')->warning(json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            $this->warn(json_encode($error, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }

        return 0;
    }

    /**
     * @param string $downloadLink
     * @return int
     * @throws \Exception
     */
    public function forceDownload(string $downloadLink)
    {
        $soundcloudSongId = $this->extractSoundcloudSongId($downloadLink);
        $path = '/var/www/html/storage/app/public/uploads/audio/soundcloud/' . $soundcloudSongId;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $downloadPath = '/var/www/html/storage/app/public/uploads/audio/soundcloud/' . $soundcloudSongId;
        $shell = shell_exec("scdl -l $downloadLink --path $downloadPath -c 2>&1");
        $file = glob("/var/www/html/storage/app/public/uploads/audio/soundcloud/$soundcloudSongId/*.mp3");
        $this->info("/var/www/html/storage/app/public/uploads/audio/soundcloud/$soundcloudSongId");
        $this->getFileName($file, $shell);
        return 0;
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

    private function extractSoundcloudSongId($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $path = ltrim($path, '/');
        return str_replace('/', '_', $path);
    }

    /**
     * @param string $downloadLink
     * @return string
     */
    private function extractAuthorFromLink(string $downloadLink): string
    {
        $soundcloudService = new SoundcloudService();
        $authorLink = explode('/', $downloadLink);
        $n = count($authorLink);
        unset($authorLink[$n - 1]);
        $authorLink = implode('/', $authorLink);
        return $soundcloudService->extractAuthorFromLink($authorLink);
    }

    /**
     * @param false|string|null $shell
     * @return array|string|string[]|null
     */
    public function extractTrackInfoFromShellOutput(false|string|null $shell): string|array
    {
        // Check if track already exist by searching for the string "already exists!"
        if (str_contains($shell, 'already exists!')) {
            $shellOutput = explode("\n", $shell);
            $shellOutput = array_filter($shellOutput);
            $shellOutput = array_values($shellOutput);
            $trackName = $shellOutput[2];
            $trackName = str_replace('Downloading ', '', $trackName);
            return str_replace('.mp3 already exists!', '', $trackName);
        }
        $shellOutput = explode("\n", $shell);
        $shellOutput = array_filter($shellOutput);
        $shellOutput = array_values($shellOutput);
        $trackName = $shellOutput[2];
        $trackName = str_replace('Downloading ', '', $trackName);
        return str_replace('.mp3 Downloaded.', '', $trackName);
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
        Log::channel('soundcloud')->info(json_encode(['song_data' => $message], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }

    /**
     * @param array|false|int|string|null $soundcloudSongId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function checkSongExist(array|false|int|string|null $soundcloudSongId)
    {
        return Song::query()->where('song_id', $soundcloudSongId)->first();
    }

    /**
     * @param Song $songExists
     * @return void
     */
    public function songExistError(Song $songExists, string $downloadLink): void
    {
        $message = [
            'songExists' => [
                'song_id' => $songExists->song_id,
                'slug' => $songExists->slug,
                'id' => $songExists->id,
                'title' => $songExists->title,
                'author' => $songExists->author,
                'genre' => $songExists->genre,
                'path' => $songExists->path,
                'image' => $songExists->image,
                'downloadLink' => $downloadLink,
            ]
        ];
        Log::channel('soundcloud')->warning(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));                // log to a file called soundcloud
        $this->warn(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }


    /**
     * @param $file
     * @param $shell
     * @return string
     * @throws \Exception
     */
    public function getFileName($file, $shell): string
    {
        try {
            return basename($file[0]);
        } catch (\Exception $e) {
            $shellOutput = explode("\n", $shell);
            $shellOutput[4] =  [
                '1. This track is not available in Germany',
                '2. Track has been removed from soundcloud',
            ];
            $this->line("<fg=bright-blue>Shell Output: " . json_encode($shellOutput, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "</>");
            throw new \Exception($e->getMessage());
        }
    }
}
