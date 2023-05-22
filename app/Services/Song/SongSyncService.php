<?php

namespace App\Services\Song;

use App\Models\Song;
use App\Services\UploadService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SongSyncService
{
    protected array $uploadedFiles = [];
    protected array $errors = [];
    protected array $songsNotinDatabase = [];
    protected array $songsNotinStorage = [];

    public function __construct()
    {
        $this->uploadedFiles = Storage::cloud()->files('music');
    }

    public function addErrors(string $error): void
    {
        $this->errors[] = $error;
    }
    /**
     * @param string $path
     * @return void
     */
    public function addSongsNotInDatabase(string $path): void
    {
        $this->songsNotinDatabase[] = $path;
    }

    /**
     * @return array
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * @return array
     */
    public function getSongsNotinDatabase(): array
    {
        return $this->songsNotinDatabase;
    }

    /**
     * @return array
     */
    public function getSongsNotinStorage(): array
    {
        return $this->songsNotinStorage;
    }



    /**
     * @param string $path
     * @return void
     */
    public function addSongsNotInStorage(string $path): void
    {
        $this->songsNotinStorage[] = $path;
    }

    public function getSongDiffs() : void
    {
        $storageSongs = $this->uploadedFiles;

        $storageSongs = array_map(function ($song) {
            return Storage::cloud()->url('curator/' . $song);
        }, $storageSongs);

        $dbSongs = Song::all()->pluck('path')->toArray();
        $diff = array_diff($storageSongs, $dbSongs);

        if (count($diff) > 0) {
            foreach ($diff as $file) {
                if (!in_array($file, $dbSongs)) {
                    $this->addSongsNotInDatabase($file);
                }
                if (!in_array($file, $storageSongs)) {
                    $this->addSongsNotInStorage($file);
                }
            }

            $diff_message = ['diff_count' => "There are " . count($diff) . " songs in storage that are not in database"];
            Log::warning(json_encode($diff_message, JSON_PRETTY_PRINT));
            $this->addErrors(json_encode($diff_message, JSON_PRETTY_PRINT));
            $notInDb = ['not_in_db' => count($this->songsNotinDatabase) . " songs not in database"];
            Log::info(json_encode($notInDb, JSON_PRETTY_PRINT));
            $this->addErrors(json_encode($notInDb, JSON_PRETTY_PRINT));
            $notInStorage = ['not_in_storage ' => count($this->songsNotinStorage) . " songs not in storage"];
            Log::info(json_encode($notInStorage, JSON_PRETTY_PRINT));
            $this->addErrors(json_encode($notInStorage, JSON_PRETTY_PRINT));
        } else {
            $message = ['message' => "No songs need to be synced"];
            Log::info(json_encode($message, JSON_PRETTY_PRINT));
            $this->addErrors(json_encode($message, JSON_PRETTY_PRINT));
        }
    }

    public function syncStorage()
    {

    }

    /**
     * @throws \Exception
     */
    public function SyncDbFull()
    {
        $uploadService = new UploadService();
        $tracks = $this->songsNotinDatabase;
        $songs = [];
        foreach ($tracks as $track) {
            $song = $uploadService->loadAndSaveSongToDb($track);
            $songs[] = $song;
        }
        $message = "Imported " . count($songs) . " songs to database";
        Log::info(json_encode(json_decode($message), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        return $songs;
    }

    public function syncDbSingleSong(string $track): Song
    {
        $uploadService = new UploadService();
        $song = $uploadService->loadAndSaveSongToDb($track);
        $path = $song->path;
        $message = ["Imported " => [
           'title' =>  $song->title,
            'slug' => $song->slug,
            'path' => $path,
        ]];
        Log::info(json_encode($message, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        return $song;
    }
}
