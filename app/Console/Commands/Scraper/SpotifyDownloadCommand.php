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
    protected $signature = 'spotify {url} {--f|force}';

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
        // if force is set , asume song does not exist in DB
        $force = $this->option('force');
        if ($force) {
            $songExists = false;
        }
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
          //  return 0;
        }

        shell_exec("pwd");

        $songDownloadLocation = "/var/www/html/storage/app/public/uploads/audio/spotify/$spotifyId/";
        // if folder does not exist create it
        if (!file_exists('/var/www/html/storage/app/public/uploads/audio/spotify')) {
            mkdir('/var/www/html/storage/app/public/uploads/audio/spotify/', 0777, true);
        }
       // $shell = shell_exec("spotdl  $url --output $songDownloadLocation --overwrite force");
        $shell = shell_exec("spotdl  $url --output $songDownloadLocation ");
        $this->info($shell);
        Log::info($shell);

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
                    $output_song = explode("\": ", $output);
                    $result = $output_song[0];
                    $fileName = $result;
                }
                if (str_contains($output, 'file already exists')) {
                    $result = $output;
                    $this->info("Raw Results _existing file: " . $result);
                    $output = str_replace("Skipping ", "", $output);
                    $output_song = explode("(file already exists)", $output);
                    $result = $output_song[0];
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
        if (count($folderContents) < 1) {
            $this->warn('No files found in ' . $songDownloadLocation);
            $message = [
                'message' => 'song with ID ' . $spotifyId . ' has not been downloaded',
                'error ' => $outputs,
                'folderContents' => $folderContents,
                'songDownloadLocation' => $songDownloadLocation,
            ];
            $this->error(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            Log::error(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return 0;
        }

        $folderContent = $folderContents[0];
        $folderContentCheck = trim(str_replace(".mp3", "", $folderContent));
        $title = explode(" - ", $folderContentCheck)[1];
        $author = explode(" - ", $folderContentCheck)[0];
        $slug = Str::slug($folderContentCheck, '_');
        $song_id = $spotifyId;
        $song_url = $url;
        $filepath = $songDownloadLocation . $folderContent;

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
        $this->info(json_encode($logInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        //  save new song to DB
        $s3Path = "https://curators3.s3.amazonaws.com/music/$slug.mp3";
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
        }

//        $this->call('song:import', [
//            '--path' => "/var/www/html/storage/app/public/uploads/audio/$slug.mp3",
//        ]);
        return 0;
    }
}
