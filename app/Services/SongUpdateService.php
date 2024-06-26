<?php

namespace App\Services;

use App\Models\Song;
use App\Services\Scraper\SoundcloudService;
use App\Services\Storage\AwsS3Service;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;

class SongUpdateService
{
    /**
     * @param Song $song
     * @return array
     */
    #[ArrayShape(['slug' => "null|string", 'title' => "null|string", 'bpm' => "mixed", 'key' => "mixed", 'energy' => "float|null", 'scale' => "mixed"])]
    public function updateBpmAndKey(Song $song): array
    {
        [$chords_scale, $energy, $bpm, $author, $key] = $this->extracted($song);

        $song->key = $key;
        $song->scale = $chords_scale;
        $song->energy = (float)$energy;
        $song->bpm = $bpm;
        if ($author && $song->author == null) {
            $song->author = $author;
        }
        $song->save();

        $slug = $song->slug;
        try {
            dump("========REMOVE FILE========");
           shell_exec("rm storage/app/public/$slug.json");
        }catch (\Exception $e) {
            Log::error($e->getMessage());
            // dump error | delete song failed
            //           // collect song detaild and mark song as missing
            // save missing songs in an array

            dd([
                'file' => __FILE__ . ' | ' . __LINE__,
                'song' => $song->slug,
                'path' => $song->path,
                'slug' => $slug,
            ]);
        }

        return [
            'slug' => $slug,
            'title' => $song->title,
            'bpm' => $song->bpm,
            'key' => $song->key,
            'energy' => $song->energy,
            'scale' => $song->scale,
        ];
    }

    /**
     * @param Song $song
     * @return Song
     * @throws \Exception
     */
    public function updateBpm(Song $song): Song
    {
        try {
            $bpm = $this->getSongBpm($song);
            $song->bpm = $bpm;
            return $song;
        }catch (\Exception $e){
            $message = [
                'try to get BPM by rhythmextractor',
                'method' => 'updateBpm',
                'song' => $song->slug,
                'error' => $e->getMessage()
            ];
            Log::error(json_encode($message, JSON_PRETTY_PRINT));
            dump($message);

            $file = $this->getFilePath($song);
            if (!$file) {
                Log::error("Audio File not found for song $song->slug");
                return $song;
            }

            $exec = shell_exec(" ./storage/app/public/streaming_rhythmextractor_multifeature $file 2>&1");
            $res = explode("\n", $exec)[5];

            $bpm = str_replace('bpm: ', '', $res);
            $bpm = round($bpm, 1);
            $song->bpm = $bpm;
            return $song;
        }

    }

    public function getSongBpm(Song $song)
    {
        [$chords_scale, $energy, $bpm, $author, $key] = $this->extracted($song);
        return $bpm;
    }

    /**
     * @param Song $song
     * @return Song
     */
    public function updateKey(Song $song): Song
    {
        [$chords_scale, $energy, $bpm, $author, $key]  = $this->extracted($song);

        $song->key = $key;
        $song->scale = $chords_scale;
        $song->save();

        $slug = $song->slug;
        shell_exec("rm storage/app/public/$slug.json");

        return $song;
    }

    /**
     * @param Song $song
     * @return string|null
     */
    public function getFilePath(Song $song): string | null
    {
        $slug = $song->slug;
        $localFilePath = "storage/app/public/uploads/audio/$slug.mp3";
        // check if file exits locally
        if (!File::exists($localFilePath)) {
            try {
                // download file from $song->path and save to storage/app/public/uploads/audio/
                $file = Http::get($song->path);
                $localFilePath = "storage/app/public/uploads/audio/$slug.mp3";
                file_put_contents($localFilePath, $file);

                if (!File::exists($localFilePath)) {
                    Log::error("Audio File not found for song $song->slug");
                    dump("Audio File not found for song $song->slug");
                    // Song Download Failed
                    // Collect song details and mark song as missing
                    // save missing songs in an array
                    // mark the song to be downloaded
                    // $this->getSongDetailsFromSoundCloud($song);

                    dump([
                        'song' => $song->slug,
                        'path' => $song->path,
                        'file' => $localFilePath,
                    ]);
                    return null;
                }
            }catch (\Exception $e){
                Log::error($e->getMessage());
                dump([
                    'song' => $song->slug,
                    'path' => $song->path,
                    'source' => $song->song_url,
                    'file' => $localFilePath,
                ]);
                // write song details to file (Id, song_url, song_id, title, author, path, slug) and mark song as missing save to root folder
                $this->addToMissingSongsFile($song);
                return null;
            }
        }
        return $localFilePath;
    }

    /**
     * @param Song $song
     * @return array
     */
    public function extracted(Song $song): array
    {

        $file = $this->getFilePath($song);
        if (!$file || $song->path === null) {
            Log::error("Audio File not found for song $song->slug");
            return [0, 0, 0, 0, 0];
        }

        $slug = $song->slug;
        $shell = shell_exec(" ./storage/app/public/streaming_extractor_music $file storage/app/public/$slug.json 2>&1");
        $shellRes = explode(' ', $shell);
        $error = str_contains($shell, 'error = Operation not permitted');
        $error2 = str_contains($shell, 'File does not exist ');

        if ($error || $error2) {
            $errorMessage = [
                'error' => $shell,
                'song' => $song->slug,
                'method' => 'extracted',
                'service' => 'SongUpdateService'
            ];
            Log::error(json_encode($errorMessage, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            return [0, 0, 0, 0, 0];
        }
        if ($shellRes[1] === '1:') {
            dump($shell);
        }

        $analysed = Storage::get("public/$slug.json");
        $res = json_decode($analysed);

        if (!$res) {
            dump($analysed);
        }

        $key = $this->getKey($res);
        $chords_scale = $this->getScale($res);


        $energy = $this->getEnergy($res);
        $bpm = $this->getBpm($res);
        $author = $this->getArtist($res);

        return [$chords_scale, $energy, $bpm, $author, $key];
    }

    /**
     * @param Song $song
     * @return Song
     */
    public function updateSongProperties(Song $song): Song
    {
        [$chords_scale, $energy, $bpm, $author, $key]  = $this->extracted($song);

        $song->key = $key;
        $song->scale = $chords_scale;
        $song->energy = $energy;
        $song->bpm = $bpm;
        if ($author && $song->author === null) {
            $song->author = $author;
        }
        $slug = $song->slug;
        try {
             shell_exec("rm storage/app/public/$slug.json");
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // dump error | delete song failed
            //           // collect song detaild and mark song as missing
            // save missing songs in an array
// mark the song to be downloaded
            dd([
                'song' => $song->slug,
                'path' => $song->path,
                'file' => $slug,
            ]);
        }

        return $song;
    }

    /**
     * @param Song $song
     * @return Song
     */
    public function getSongEnergy(Song $song) : Song
    {
        [$chords_scale, $energy, $bpm, $author, $key] = $this->extracted($song);
        $song->energy = (float)$energy;
        return $song;
    }

    public function updateDuration(array|string $slug = null): array
    {
        if ($slug === null) {
            $songs = Song::query()->whereNull('duration')->get();
        } else {
            $songs[] = Song::query()->where('slug', $slug)->first();
        }
        $message = [
            'slug' => $slug,
            'message' => 'update duration',
            'songs' => count($songs)
        ];
        Log::info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $completed = [];
        /** @var Song $song */
        foreach ($songs as $song) {
            $updatedSong = $this->getSongDuration($song);
            $updatedSong->save();
            $completed[] = [
                'title' => $updatedSong->title,
                'author' => $updatedSong->author,
                'duration' => $updatedSong->duration,
                'slug' => $updatedSong->slug,
                'image' => $updatedSong->image,
            ];
        }

        return $completed;
    }

    /**
     * @param $data
     * @param Song|null $song
     * @return void
     * @throws \Exception
     */
    public function setImageFromSong($data, Song|null $song): void
    {
        if ($song->image === null) {
            // save image to storage/app/public/images/
            $imageName = $song->slug . '.jpeg';
            $image = "storage/app/public/images/$imageName";
            file_put_contents($image, $data);
            $storageService = new AwsS3Service();
            $imagePath = $storageService->putObject($image, 'images');
            $song->image = $imagePath;
        }
    }

    /**
     * @param mixed $songPath
     * @return array
     */
    public function getAnalyze(mixed $songPath): array
    {
        $path = $songPath;
        $getID3 = new \getID3;
        return $getID3->analyze($path);
    }

    /**
     * @param $fileInfo
     * @param Song|null $song
     * @return Song
     */
    public function getInfoFromId3v2Tags($fileInfo, Song|null $song): Song
    {
        $idv = $fileInfo['tags']['id3v2'] ?? null;
        if ($idv && count($idv) < 5) {
            $title = $idv['title'][0] ?? null;
            // remove everything after the | in the title
            if (str_contains($title, '|')) {
                $title = substr($title, 0, strpos($title, '|'));
            }
            $title = trim($title);
            $song->title = $title;
            return $song;
        }
        $genres = $idv['genre'] ?? $song->genre;
        $artist = $idv['artist'] ?? $song->author;
        $title = $idv['title'][0] ?? $song->title;

        // remove everything after the | in the title
        if (str_contains($title, '|')) {
            $title = substr($title, 0, strpos($title, '|'));
        }
        $title = trim($title);
        $comment = $idv['comment'][0] ?? $song->comment;
        if ($song->genre === null) {
            $song->genre = $genres;
        }
        if ($song->author == null) {
            // if author is an array get all authors and implode seperated by &
            if (is_array($artist)) {
                $artist = implode(' & ', $artist);
            }
            $song->author = $artist;
        }

        $song->title = $title;
        $song->comment = json_encode($comment, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($song->image === null) {
            try {
                $this->setImageFromSong($fileInfo['comments']['picture'][0]['data'], $song);
            } catch (\Exception $e) {
                $song->image = null;
                $message = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'class::method' => $e->getTrace()[0]['class'] . '::' . $e->getTrace()[0]['function'],
                    'location' => __FILE__ . '::' . __CLASS__ . '::' . __METHOD__,
                ];
                Log::warning(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

                return $song;
            }
        }
        return $song;
    }

    public function getSongDetailsFromSoundCloud(Song $song): void
    {
        $searchQuery = $song->title;

        $soundcloud = new SoundcloudService();
        // first part of title = artis , second part of title = title
        $titles = explode(' - ', $searchQuery);
        $artists = $titles;
        // last element of array is the title
        $songTitle = $titles[count($titles) - 1];
        // the resit is artist
        unset($titles[count($titles) - 1]);

        $searchQuery = Str::slug($searchQuery);
        $songTitle = Str::slug($songTitle);
        $params[] = $songTitle;
        foreach ($titles as $key => $title) {
            $titles[$key] = Str::slug($title);
            $params[] = $titles[$key];
        }

        $link = $soundcloud->getTrackLink($searchQuery, $params);

        if ($link === null || $link === '') {
            dump(['id' => $song->id, 'title' => $songTitle, 'artist' => $artists, 'path' => $song->path]);
            return;
        }
        $message = [
            "message" => "call soundcloud download command scrape:sc",
            "url" => $link,
            "file" => __FILE__,
            "method" => __METHOD__,
            "class" => __CLASS__,
        ];
        dump($message);
        Log::warning(json_encode($message, JSON_PRETTY_PRINT));
        Artisan::call('scrape:sc', [
            'link' => $link
        ]);
        $slug = Str::slug($songTitle, '_');

        if ($slug === '' ){
            return;
        }
      }

    /**
     * @param string $file
     * @param string $slug
     * @return string|null
     * @throws \Exception
     */
    public function getExistingImageFromFile(string $file, string $slug): string|null
    {
        $imageFolder = env('IMAGE_PATH', 'storage/app/public/uploads/images');
        $process = Process::run(['ffmpeg', '-i', $file, '-an', '-vcodec', 'copy', '-f', 'image2', '-y', $imageFolder . '/' . $slug . '.jpeg']);
        if (!$process->successful() ){
            // last line of error output is the error
            $errorOutput = explode("\n", $process->errorOutput());
            $ffmpegMessage = [
                'message' => "Fail to get image from file $slug",
                'file' => __FILE__ . ' | ' . __LINE__,
                'service' => __CLASS__,
                'method' => __METHOD__,
                'song' => $file,
                'error_ffmpeg' => $errorOutput[count($errorOutput) - 2]
            ];
            //dump($ffmpegMessage);
            Log::critical(json_encode($ffmpegMessage, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            throw new \Exception(json_encode($ffmpegMessage, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }
        // check if image exists in image folder
        $localImage = $imageFolder . '/' . $slug . '.jpeg';
        if (!file_exists($localImage)) {
            Log::info("Image was not successfully extracted from file by ffmpeg");
            dump('Image was not successfully extracted from file by ffmpeg');
            return null;
        }

        try {
            $storageService = new AwsS3Service();
            $imagePath = $storageService->putObject($localImage, 'images');
            // check if image has successfully uploaded
            $imageCheck = Http::get($imagePath);
            if ($imageCheck->status() !== 200) {
                Log::error(json_encode(
                    [
                        'error' => "image did not upload to minio $imagePath$localImage",
                        'file' => __FILE__ . ' | ' . __LINE__,
                    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
                ));
                dump('image did not upload');
                return null;
            }
        }catch (\Exception $e){
            Log::critical(json_encode([
                'error' => $e->getMessage(),
                'file' => __FILE__ . ' | ' . __LINE__,
            ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            dump($e->getMessage());
            return null;
        }
        return $imagePath;
    }

    /**
     * @param Song $song
     * @return Song
     * @throws \Exception
     */
    public function getSongImage(Song $song): Song
    {
        $file = $song->path;
        try {
            $image = $this->getExistingImageFromFile($file, $song->slug);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            $song->image = null;
            $song->save();
            throw new \Exception($e->getMessage());
        }
        $song->image = $image;
        return $song;
    }

    /**
     * @param Song $song
     * @return Song
     */
    public function getSongDuration(Song $song): Song
    {
        $songPath = $this->getFilePath($song);
        if ($songPath === null) {
            Log::warning("Missing Audio File. File not found locally. Could not obtain duration for $song->slug");
            return $song;
        }
        $fileInfo = $this->getAnalyze($songPath);

        try {
            $duration = $fileInfo['playtime_seconds'] ?? null;
            $song->duration = $duration;
            $song->save();
            return $song;
        } catch (\Exception $e) {
            Log::critical("Failed Update Song Duration: $songPath" . $e->getMessage());
            dump("Failed Update Song Duration: $songPath" . $e->getMessage());
        }
        return $song;
    }

    /**
     * @param mixed $res
     * @return mixed
     */
    public function getKey(mixed $res): mixed
    {
        try {
            return $this->getTonal($res)->key_key ?? $this->getTonal($res)->chords_key;
        } catch (\Exception $e) {
            $this->dumpErrorMessage($res, $e);
            return null;
        }
    }

    /**
     * @param mixed $res
     * @return mixed
     */
    public function getScale(mixed $res): mixed
    {
        try {
            return $this->getTonal($res)->key_scale ?? $this->getTonal($res)->chords_scale;
        } catch (\Exception $e) {
            dump($res);
            dump($e->getMessage());
            return null;
        }
    }

    /**
     * @param mixed $res
     * @return mixed
     */
    public function getTonal(mixed $res): mixed
    {
        try {
            return $res->tonal;
        } catch (\Exception $e) {
            dump($res);
            dump($e->getMessage());
            return null;
        }
    }

    /**
     * @param mixed $res
     * @return mixed
     */
    public function getEnergy(mixed $res): mixed
    {
        try {
            return $res->lowlevel->spectral_energy->max;
        } catch (\Exception $e) {
            $this->dumpErrorMessage($res, $e);
            return null;
        }
    }

    /**
     * @param mixed $res
     * @return null
     */
    public function getArtist(mixed $res)
    {
        try {
            return $res->metadata->tags->artist[0] ?? null;
        } catch (\Exception $e) {
            $this->dumpErrorMessage($res, $e);
            return null;
        }
    }

    /**
     * @param $res
     * @param \Exception $e
     * @return void
     */
    public function dumpErrorMessage($res, \Exception $e): void
    {
        dump([
         //   'song' => $res->metadata ?? $res->metadata->tags,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'class::method' => $e->getTrace()[0]['class'] . '::' . $e->getTrace()[0]['function'],
            'location' => __FILE__ . '::' . __CLASS__ . '::' . __METHOD__,
        ]);
    }

    /**
     * @param mixed $res
     * @return float|int
     */
    public function getBpm(mixed $res): int|float
    {
        try {
            return round($res->rhythm->bpm * 2) / 2;
        } catch (\Exception $e) {
            $this->dumpErrorMessage($res, $e);
            return 0;
        }
    }

    /**
     * @param Song $song
     * @return void
     */
    public function addToMissingSongsFile(Song $song): void
    {
        $songDetails = [
            'id' => $song->id,
            'song_url' => $song->song_url,
            'song_id' => $song->song_id,
            'title' => $song->title,
            'author' => $song->author,
            'path' => $song->path,
            'slug' => $song->slug,
        ];
        $songDetails = json_encode($songDetails, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        dump($songDetails);
        $missingSongs = fopen('missing_songs.txt', 'a');
        fwrite($missingSongs, $songDetails);
        fclose($missingSongs);
    }
}
