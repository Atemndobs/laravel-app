<?php

namespace App\Services;

use App\Jobs\ClassifySongJob;
use App\Models\Song;
use Illuminate\Http\UploadedFile;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadService
{
    protected Song $song;
    public string $deletItem = '';
    public array $deletables = [];
    public bool $rclone = false;

    /**
     * @return array
     */
    public function getDeletables(): array
    {
        return $this->deletables;
    }

    /**
     * @param  array  $deletables
     * @return UploadService
     */
    public function setDeletables(array $deletables): UploadService
    {
        $this->deletables = $deletables;

        return $this;
    }

    public function addDeletables($deleteItem): array
    {
        $this->deletables[] = $deleteItem;

        return $this->deletables;
    }

    public function uploadSong(UploadedFile $track)
    {
        $song = new Song();
        $this->processAndSaveSong($track, $song);
        $existingSong = $this->getExistingSong($song);

        if ($existingSong) {
            return $existingSong;
        }
        $song->status = 'uploaded';
        $song->save();
        ClassifySongJob::dispatch($song->title);

        return $song->getDirty();
    }

    /**
     * @param  array  $tracks
     * @return array
     */
    public function batchUpload(array $tracks)
    {
        $response = [];
        foreach ($tracks as $file) {
            $song = new Song();
            if ($file->isValid()) {
                $filledSong = $this->processAndSaveSong($file, $song);
                $response[] = $filledSong;

                ClassifySongJob::dispatch($filledSong->title);
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
        $this->deletables = [];
        foreach ($tracks as $file) {
            $song = new Song();
            try {
                $file_name = $this->getFullSongPath($file, $song);
            } catch (\Exception $e) {
                $error[] = [
                    'error' => $e->getMessage(),
                    'file' => $file,
                ];
                dump([$error]);
                Log::error($e->getMessage());
                continue;
            }
            $existingSong = $this->getExistingSong($song);
            if ($existingSong) {
                $response[] = [
                    'file' => $file,
                    'file_name' => $file_name,
                    'id' => $existingSong->id,
                    'title' => $existingSong->title,
                    'slug' => $existingSong->slug,
                    'path' => $existingSong->path,
                ];
                Log::warning('Song already exists: '.$file_name);
                continue;
            }

            $song->status = 'imported';
            $ext = substr($file_name, -3);
            $type = $ext;
            $source = 'imported';
            $this->getSongImage($file_name, $song);
            $this->fillSong($source, $song, $type, $file_name, $ext);

            $song->save();
            $response[] = $song;
            $this->deletables[] = $this->deletItem;
            ClassifySongJob::dispatch($file_name);
        }
        return $response;
    }

    /**
     * @param  Song  $song
     * @return mixed
     */
    protected function getExistingSong(Song $song)
    {
        return Song::where('path', '=', $song->path)->first();
    }

    /**
     * @param  mixed  $file
     * @param  Song  $song
     * @return Song
     */
    protected function processAndSaveSong(mixed $file, Song $song): Song
    {
        $file_name = $file->getClientOriginalName();
        $type = $file->getMimeType();
        $source = 'uploaded';
        $api_url = env('APP_URL').'/api/songs/match/';
        $ext = substr($file_name, -4);
        $new_file_name = str_replace($ext, '', $file_name);
        $new_file_name = Str::slug($new_file_name, '_');
        $song->slug = $new_file_name;
        $slug = $new_file_name;
        $new_file_name .= $ext;

        $file_path = $file->storeAs('audio', $new_file_name, 'public');
        $full_path = asset(Storage::url($file_path));
        $song->status = 'uploaded';
        $song->path = $full_path;

        $existingSong = $this->getExistingSong($song);

        if ($existingSong) {
            return $existingSong;
        }

        $song->related_songs = $api_url.$slug;
        $this->fillSong($source, $song, $type, $file_name, $ext);
        $song->save();

        return $song;
    }

    /**
     * @param  string  $source
     * @param  Song  $song
     * @param  string|null  $type
     * @param  string  $name
     * @param  string  $ext
     * @return void
     */
    public function fillSong(string $source, Song $song, ?string $type, string $name, string $ext): void
    {
        $name = str_replace(".$ext", '', $name);
        $fields = [
            'link' => $source,
            'path' => $song->path,
            'slug' => $song->slug,
            'source' => $type,
            'title' => $name,
            'extension' => $ext,
        ];
        $song->fill($fields);
    }

    /**
     * @param  mixed  $file
     * @param  Song  $song
     * @return array|mixed|string|string[]
     */
    protected function getFullSongPath(mixed $file, Song $song): mixed
    {
        // $path_to_store = setting('site.path_audio') ?? env('PATH_AUDIO', 'audio');
        $path_to_store = env('PATH_AUDIO', 'uploads/audio');
        $file_name = substr($file, strrpos($file, '/') + 1);
        $ext = substr($file_name, -4);
        $file_name = str_replace($ext, '', $file_name);
        $file_name = Str::slug($file_name, '_');
        $file_name .= $ext;
        $full_path =  "storage/app/public/$path_to_store/$file_name";     //
        // rename file to full path and save on minio
        rename($file, $full_path);
        Storage::cloud()->put("music/$file_name", file_get_contents($full_path));
        $storage_path = Storage::cloud()->url("curator/music/$file_name");
        // delete  file from local storage
        Storage::delete($full_path);
        
        $api_url = env('APP_URL').'/api/songs/match/';
        $slug = Str::slug($file_name, '_');
        $song->path = $storage_path;
        $song->slug = $slug;
        $song->related_songs = $api_url.$slug;

        return $file_name;
    }

    private function getSongImage(mixed $file_name, Song $song): void
    {
        // check if image exist in minio and save it
        $image = substr($file_name, 0, -4);
        $image .= '.jpg';
        $image = "curator/images/$image";
        // check if image exist in minio
        if (!Storage::cloud()->exists($image)) {
            return;
        }
        $image =  Storage::cloud()->url($image);
        $song->image = $image;
    }

    private function checkExistingSongStorage(?string $path) : bool
    {

        $base_url = env('APP_ENV') == 'local' ? 'http://nginx' : env('APP_URL');
        $url = str_replace('mage.tech', 'host.docker.internal', $path);
        Log::info("Checking Song from Storage : $path");

        Log::info(json_encode([
            'process' => 'UploadService::checkExistingSongStorage',
            'args' => func_get_args(),
            'path' => $path,
            'base_url' => $base_url,
            'url' => $url,
        ]));

        if (!str_contains($url, 'http')) {
            $url = $base_url.'/storage/audio'.$url;
        }
        $request = Http::get($url);
        $status = $request->status();
        Log::info("Status : $status");
        if ($status === 200) {
            dump(['status' => $status, 'url' => $url]);
            Log::info("Song Exists in Storage : $path");
            return true;
        }
        return false;
    }

    public function setRclone(bool $true): bool
    {
        return $this->rclone = $true;
    }
}
