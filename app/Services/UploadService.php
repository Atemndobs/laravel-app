<?php

namespace App\Services;

use App\Jobs\ClassifySongJob;
use App\Models\Song;
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
        $song = new Song();
        $songUpdateService = new SongUpdateService();
        $slug = $this->getSlugFromFilePath($track);
        $existingSong = $this->getExistingSong($slug);
        if ($existingSong) {
            return $existingSong;
        }
        $fileInfo = $songUpdateService->getAnalyze($track);
        $songWithInfo = $songUpdateService->getInfoFromId3v2Tags($fileInfo, $song);
        $processedSong = $this->processSong($track, $songWithInfo);
        $songWithImage =  $this->getSongImage($track, $processedSong);
        // save song and delete file
        $songWithImage->save();
        if (File::delete($track)){
            Log::info('Deleted file: '.$track);
            dump('Deleted file: '.$track);
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
                dump([$error]);
                Log::error($e->getMessage());
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
        ];
        Log::info(json_encode($message));
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

        try {
            $imagePath = $songUpdateService->getExistingImageFromFile($file, $song->slug);
            $song->image = $imagePath;
            Log::info("Image path : $imagePath");
            return $song;
        } catch (\Exception $e) {
            Log::error(json_encode([
                'message' => "Image not found in minio : $song->slug",
                'error' => $e->getMessage(),
                'file' => $file,
            ]));
            $this->addMissingImages($file);
            return $song;
        }
    }

    /**
     * @param mixed $file
     * @return string
     */
    public function getSlugFromFilePath(mixed $file): string
    {
        $file_name = substr($file, strrpos($file, '/') + 1);
        $ext = substr($file_name, -4);
        $file_name = str_replace($ext, '', $file_name);
        return Str::slug($file_name, '_');
    }
}
