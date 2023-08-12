<?php

namespace App\Console\Commands\Scraper;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SpotifyDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spotify {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download Playlists from Spotify and maybe Youtube too';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Downloading Playlists...');
        $url = $this->argument('url');
        $spotifyId = explode("track/", $url);
        $spotifyId = $spotifyId[1];
        $spotifyId = explode("?", $spotifyId);
        $spotifyId = $spotifyId[0];
        // check if song exists in DB
        $songExists = \App\Models\Song::where('song_id', $spotifyId)->first();
        if ($songExists) {
            $this->error('Song with ID ' . $spotifyId . ' already exists in DB.');
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

            $this->warn(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return 0;
        }

        $songDownloadLocation = "/var/www/html/storage/app/public/uploads/audio/";

        $shell = shell_exec("spotdl  $url --output $songDownloadLocation");
//        $this->info($shell);
//        Log::info($shell);
        try {
            $outputs = explode("\n", $shell);
            $result = "";
            $filepath = "";
            $fileName = "";
            foreach ($outputs as $output) {
                if (str_contains($output, 'Downloaded')) {
                    $result = $output;
                    $this->info("Raw Results : " . $result);
                    $output = str_replace("Downloaded \"", "", $output);
                    $output = explode("\": ", $output);
                    $result = $output[0];
                    $fileName = $result;
                }
                if (str_contains($output, 'file already exists')) {
                    $result = $output;
                    $this->info("Raw Results _existing file: " . $result);
                    $output = str_replace("Skipping ", "", $output);
                    $output = explode("(file already exists)", $output);
                    $result = $output[0];
                    $fileName = $result;
                }
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            $error = [
                'error' => "Spotify Download Failed",
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ];
            Log::error(json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        $folderContents = array_values(array_diff(scandir($songDownloadLocation), array('..', '.')));
        if ($folderContents < 1) {
            $this->error('No files found in ' . $songDownloadLocation);
            return 0;
        }
        foreach ($folderContents as $folderContent) {
            // remove rile extension
            $folderContentCheck = trim(str_replace(".mp3", "", $folderContent));
            $fileName = trim($fileName);
            if (str_contains($folderContentCheck, $fileName)) {
                $this->info('File with name ' . $folderContent . ' has been saved in  ' . $songDownloadLocation);
                $filepath = $songDownloadLocation . $folderContent;
            }
        }
        if ($filepath == "") {
            $message = [
                'message' => 'File not found in ' . $songDownloadLocation,
                'folderContents' => $folderContents,
                'fileName' => $fileName,
                'filepath' => $filepath,
            ];
            $this->error(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return 0;
        }

        $title = explode(" - ", $result)[1];
        $author = explode(" - ", $result)[0];
        $song_id = $spotifyId;
        $song_url = $url;
        $slug = Str::slug($result, '_');
        //  save new song to DB
        $song = new \App\Models\Song();
        $song->title = $title;
        $song->author = $author;
        $song->song_url = $song_url;
        $song->song_id = $song_id;
        $song->slug = $slug;
        $song->path = $filepath;
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

        $logInfo = [
            'result' => $result,
            'fileName' => $fileName,
            'songDownloadLocation' => $songDownloadLocation,
            'filepath' => $filepath,
            'title' => $title,
            'author' => $author,
            'song_id' => $song_id,
            'song_url' => $song_url,
            'slug' => $slug,
        ];
        Log::info(json_encode($logInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return 0;
    }
}
