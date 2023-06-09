<?php

namespace App\Services;

use App\Jobs\ClassifySongJob;
use App\Models\Song;
use App\Services\Birdy\SpotifyService;
use App\Services\Storage\MinioService;
use Illuminate\Http\UploadedFile;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function Amp\ByteStream\buffer;
use function Clue\StreamFilter\remove;

class UploadService
{
    public array $missingImages = [];

    /**
     * @return array
     */
    public function getMissingImages() : array
    {
        return $this->missingImages;
    }

    /**
     * @param  string  $missingImage
     */
    public function addMissingImages(string $missingImage) : void
    {
        $this->missingImages[] = $missingImage;
    }

    /**
     * @param string $track
     * @return Song
     * @throws \Exception
     */
    public function uploadSong(string $track): Song
    {
        $slug = $this->getSlugFromFilePath($track);
        $existingSong = $this->getExistingSong($slug);
        if ($existingSong) {
            return $existingSong;
        }

        $song = $this->loadAndSaveSongToDb($track);

        if (File::delete($track)){
            $message = [
                'message' => 'File deleted',
                'file' => $track,
                'Command' => 'song:import, line: ' . __LINE__,
            ];
            Log::info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        };

        return $song;
    }

    /**
     * @param array $tracks
     * @return array
     * @throws \Exception
     */
    public function batchUpload(array $tracks): array
    {
        $response = [];
        foreach ($tracks as $file) {
            $song = new Song();
            if (file_exists($file) ) {
                $filledSong = $this->processSong($file, $song);
                $response[] = $filledSong;
            }
        }

        return $response;
    }

    /**
     * @param  array  $tracks
     * @return array
     */
    public function importSongs(array $tracks): array
    {
        $response = [];
        foreach ($tracks as $file) {
            $slug = $this->getSlugFromFilePath($file);
            if ($this->getExistingSong($slug)) {
                Log::warning('Song already exists: '. $slug);
                continue;
            }
            $song = new Song();
            try {
                $this->getFullSongPath($file, $song);
            } catch (\Exception $e) {
                $error[] = [
                    'error' => $e->getMessage(),
                    'file' => $file,
                ];
                Log::error(json_encode($error, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                continue;
            }

            $song->status = 'imported';
            $this->getSongImage($file, $song);
            $song->save();
            $response[] = $song;
        }
        return $response;
    }

    /**
     * @param string $slug
     * @return Song|null
     */
    protected function getExistingSong(string $slug) : Song | null
    {
        return Song::query()->where('slug', '=', $slug)->get()->first();
    }

    /**
     * @param mixed $file
     * @param Song $song
     * @return Song
     * @throws \Exception
     */
    protected function processSong(mixed $file, Song $song): Song
    {
        $songWithPath = $this->getFullSongPath($file, $song);
        $ext = substr($file, -3);
        $link = 'uploaded';
        $songWithPath->link = $link;
        $songWithPath->source = $ext;
        $songWithPath->extension = $ext;
        return $songWithPath;
    }

    /**
     * @param mixed $file
     * @param Song $song
     * @return Song
     * @throws \Exception
     */
    protected function getFullSongPath(mixed $file, Song $song): Song
    {
        $slug = $this->getSlugFromFilePath($file);
        $storageService = new MinioService();
        $storage_path = $storageService->putObject($file);

        $message = [
            'FILE NAME' => $file,
            'FILE CLOUD URL' => $storage_path,
            'FILE SLUG' => $slug,
        ];
        Log::info(json_encode($message, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        $api_url = env('APP_URL').'/api/songs/match/';
        $song->path = $storage_path;
        $song->slug = $slug;
        $song->related_songs = $api_url.$slug;
        return $song;
    }

    /**
     * @param mixed $file
     * @param Song $song
     * @return Song
     */
    private function getSongImage(mixed $file, Song $song): Song
    {
        $songUpdateService = new SongUpdateService();
        $imagePath = $songUpdateService->getExistingImageFromFile($file, $song->slug);

        if ($imagePath) {
            $song->image = $imagePath;
            Log::info(json_encode([
                'message' => "Image found in minio : $song->slug",
                'file' => $file,
                'image' => $imagePath,
            ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            return $song;
        }
        try {
            Log::error(json_encode([
                'message' => "Image not found in minio : $song->slug",
                'file' => $file,
            ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            return $this->getImageFromSpotify($song);
        } catch (\Exception $e) {
            Log::error(json_encode([
                'message' => "Image not found in minio : $song->slug",
                'error' => $e->getMessage(),
                'file' => $file,
            ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            return $song;
        }
    }

    /**
     * @param Song $song
     * @return Song
     */
    public function getImageFromSpotify(Song $song) : Song
    {
        Log::info('Getting image from spotify');
        $spotifyService = new SpotifyService();
        try {
            if ((int)$song->title === null || $song->title === '') {
                $image =  $spotifyService->findSong($song->slug)->album->images[0]->url;
                $title =  $spotifyService->findSong($song->slug)->name;
                $artist =  $spotifyService->findSong($song->slug)->artists[0]->name;
                $song->image = $image;
                $song->title = $title;
                $song->author = $artist;
                return $song;
            }
            $image =  $spotifyService->getImageFromTitle($song->title);
            $song->image = $image;
            return $song;
        }catch (\Exception $e) {
            Log::error(json_encode($e->getMessage(), JSON_PRETTY_PRINT));
            return $song;
        }
    }

    /**
     * @param mixed $file
     * @return string
     */
    public function getSlugFromFilePath(mixed $file): string
    {
        $file_name = substr($file, strrpos($file, '/'));
        $ext = substr($file_name, -4);
        $file_name = str_replace($ext, '', $file_name);
        return Str::slug($file_name, '_');
    }

    /**
     * @param $file
     * @return Song
     * @throws \Exception
     */
    public function loadAndSaveSongToDb($file): Song
    {
        $song = new Song();
        $songUpdateService = new SongUpdateService();
        $fileInfo = $songUpdateService->getAnalyze($file);
        $songWithInfo = $songUpdateService->getInfoFromId3v2Tags($fileInfo, $song);
        $processedSong = $this->processSong($file, $songWithInfo);
        if ($processedSong->image === null) {
            $processedSong = $this->getSongImage($file, $processedSong);
        }
        $processedSong->save();
        $tempImage = $this->getTempImagePath($processedSong->image);
        if (file_exists($tempImage)) {
            unlink($tempImage);
        }
        return $song;
    }

    /**
     * @param string $path
     * @return Song
     * @throws \Exception
     */
    public function loadFromUrlAndSaveSongToDb(string $path): Song
    {
        $name = basename($path);

        $audioPath = $this->getTempAudioPath($path);
        if (str_contains($name, ' ')) {
            $storageService = new MinioService();
            $storageService->deleteMusic($name);
            $errorMassage = [
                'message' => 'File name contains spaces',
                'file' => $name,
            ];
            Log::error(json_encode($errorMassage, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            throw new \Exception('File name contains spaces');
        }

        $song = new Song();
        $song->path = $path;
        $songUpdateService = new SongUpdateService();


        if (!file_exists($audioPath)) {
            file_put_contents($audioPath, fopen($path, 'r'));
        }

        $ext = substr($name, -3);
        $song->source = $ext;
        $song->extension = $ext;
        $song->slug = $this->getSlugFromFilePath($name);
        $fileInfo = $songUpdateService->getAnalyze($audioPath);
        $processedSong = $songUpdateService->getInfoFromId3v2Tags($fileInfo, $song);
        if ($processedSong->image === null) {
            $processedSong = $this->getSongImage($audioPath, $processedSong);
        }
        $processedSong->author = Str::ascii($processedSong->author);
        $processedSong->title = Str::ascii($processedSong->title);
        $processedSong->save();

        $tempImage = $this->getTempImagePath($processedSong->image);
        if (file_exists($tempImage)) {
            unlink($tempImage);
        }
        unlink($audioPath);
        return $song;
    }

    /**
     * @throws \Exception
     */
    public function loadAndSaveSongToDbFull($file): Song
    {
        $processedSong = $this->loadAndSaveSongToDb($file);
        $songUpdateService = new SongUpdateService();
        $processedSong = $songUpdateService->getSongDuration($processedSong);
        $processedSong = $songUpdateService->updateSongProperties($processedSong);
        if ($processedSong->bpm === null) {
            $processedSong = $songUpdateService->updateBpm($processedSong);
        }
        $processedSong->save();
        return $processedSong;
    }

    /**
     * @param string $path
     * @return string
     */
    public function getTempAudioPath(string $path): string
    {
        $baseName = basename($path);
        $audioDir = env('AUDIO_PATH') ?? '/var/www/html/storage/app/public/uploads/audio';
        return $audioDir . '/' . $baseName;
    }

    /**
     * @param string $path
     * @return string
     */
    public function getTempImagePath(string $path): string
    {
        $baseName = basename($path);
        $imageDir = env('IMAGE_PATH') ?? '/var/www/html/storage/app/public/uploads/images';
        return $imageDir . '/' . $baseName;
    }
}
