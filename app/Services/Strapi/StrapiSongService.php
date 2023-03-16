<?php

namespace App\Services\Strapi;

use App\Models\File;
use App\Models\Song;
use App\Services\UploadService;
use Dbfx\LaravelStrapi\LaravelStrapi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StrapiSongService
{
    public string $baseUrl;

    public LaravelStrapi $laravelStrapi;

    public function __construct()
    {
        $this->baseUrl = config('strapi.url');
        $url = env('MEILISEARCH_HOST');

        $this->laravelStrapi = new LaravelStrapi();
    }

    /**
     * @return array
     */
    public function importStrapiUploads()
    {
        $prepareImports = [];

        try {
            // http://host.docker.internal:1337
            $response = Http::get('http://host.docker.internal:1337/api/upload/files');
            $strapiUploads = $response->json();
            $prepareImports = $this->prepareImports($strapiUploads);
        } catch (\Exception $exception) {
            info($exception->getMessage());
        }

        return $prepareImports;
    }

    /**
     * @param  array  $song
     * @return array
     */
    public function importStrapiSong(array $song)
    {
        return $this->importAndSave($song, [], new UploadService());
    }

    /**
     * @param  string  $title
     * @return mixed
     */
    public function existing(string $title)
    {
        return  Song::where('title', '=', $title)->first();
    }

    /**
     * @param $strapiUploads
     * @return array
     */
    public function prepareImports($strapiUploads): array
    {
        $response = [];
        $uploadService = new UploadService();
        foreach ($strapiUploads as $upload) {
            $response = $this->importAndSave($upload, $response, $uploadService);
        }

        return $response;
    }

    /**
     * @param  mixed  $upload
     * @param  array  $response
     * @param  UploadService  $uploadService
     * @return array
     */
    protected function importAndSave(mixed $upload, array $response, UploadService $uploadService): array
    {
        $base = 'http://mage.tech:1337';
        $song = new Song();

        if ($existing = $this->existing($upload['name'])) {
            $response[] = $existing;
        } else {
            $song->title = $upload['name'];
            $song->path = $base.$upload['url'];
            $song->link = $upload['hash'];
            $song->source = $upload['provider'];
            $song->status = 'queued';

            $file_name = $song->title;
            $ext = substr($file_name, -4);
            $new_file_name = str_replace($ext, '', $file_name);
            $new_file_name = Str::slug($new_file_name, '_');
            $new_file_name .= $ext;
            $song->slug = Str::slug($new_file_name, '_');

            $api_url = env('APP_URL').'/api/songs/match/';
            $song->related_songs = $api_url.$song->slug;

            $uploadService->fillSong(
                $song->source,
                $song,
                $upload['mime'],
                $upload['name'],
                $upload['ext']
            );
            $song->save();
            $response[] = $song;

            $this->dowloadStrapiSong($song);
        }

        return $response;
    }

    public function dowloadStrapiSong(Song $song)
    {
        $slug = $song->slug;
        $url = $song->path;
        // $req_url = str_replace('http://mage.tech', 'http://localhost', $url);
        $req_url = str_replace('http://mage.tech', 'http://host.docker.internal', $url);

        $full_path = $this->getSongByLink($slug, $req_url);
        $song->path = $full_path;
        $song->save();
        $this->deleteStrapiFile($song);

        return $song->path;
    }

    public function deleteStrapiFile(Song $song)
    {
        $file = File::where('name', '=', $song->title)->first();
        $id = $file->id;
        $req = Http::delete("http://host.docker.internal:1337/api/upload/files/$id");
        $file->delete();

        return $req->status();
    }

    /**
     * @param  string|null  $slug
     * @param  array|string|null  $req_url
     * @return string
     */
    public function getSongByLink(?string $slug, array|string|null $req_url): string
    {
        $ext = substr($slug, -3);
        $new_file_name = str_replace($ext, '', $slug);
        $new_file_name = Str::slug($new_file_name, '_');
        $filename = "$new_file_name.$ext";

        file_put_contents("storage/app/public/audio/$filename", fopen($req_url, 'r'));

        return asset(Storage::url("audio/$filename"));
    }
}
