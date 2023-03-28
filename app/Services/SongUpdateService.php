<?php

namespace App\Services;

use App\Models\Song;
use App\Services\Scraper\SoundcloudService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
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
        $song->author = $author;
        $song->save();

        $slug = $song->slug;
        shell_exec("rm storage/app/public/$slug.json");

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
     */
    public function updateBpm(Song $song): Song
    {
   // dd('updateBpm');
        $file = $this->getFilePath($song);
        $exec = shell_exec(" ./storage/app/public/streaming_rhythmextractor_multifeature storage/app/public/$file 2>&1");
        $res = explode("\n", $exec)[5];

        $bpm = str_replace('bpm: ', '', $res);
        $bpm = round($bpm, 1);
        $song->bpm = $bpm;
        $song->save();

        return $song;
    }

    /**
     * @param Song $song
     * @return Song
     */
    public function updateKey(Song $song): Song
    {
        [$chords_scale, $key] = $this->extracted($song);

        $song->key = $key;
        $song->scale = $chords_scale;
        $song->save();

        $slug = $song->slug;
        shell_exec("rm storage/app/public/$slug.json");

        return $song;
    }

    /**
     * @param Song $song
     * @return string
     */
    public function getFilePath(Song $song): string
    {
        $path = $song->path;
        // extract
        return str_replace('http://mage.tech:8899/storage/', '', $path);
    }

    /**
     * @param Song $song
     * @return array
     */
    public function extracted(Song $song): array
    {

        $file = $this->getFilePath($song);
        $slug = $song->slug;
        $shell = shell_exec(" ./storage/app/public/streaming_extractor_music storage/app/public/$file storage/app/public/$slug.json 2>&1");
        $shellRes = explode(' ', $shell);

        $error = str_contains($shell, 'error = Operation not permitted');
        $error2 = str_contains($shell, 'File does not exist ');

        if ($error || $error2) {
            dump($error);
//            $path = str_replace('mp3', '.mp3', $slug);
//            shell_exec("rm storage/app/public/audio/$path");
//            $song->delete();
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

        $key_key = $res->tonal->key_key;
        // $key_scale = $res->tonal->key_scale;
        // $chords_key = $res->tonal->chords_key;
        $key = $key_key;
        $chords_scale = $res->tonal->chords_scale;

        $energy = $res->lowlevel->spectral_energy->max;
        $bpm = round($res->rhythm->bpm * 2) / 2;
        $author = $res->metadata->tags->artist ?? '';
        if ($author !== '') {
            $author = $res->metadata->tags->artist[0];
        } else {
            $author = $song->author;
        }

//        dump([
//            'song' => $song->slug,
//            'bpm' => $bpm,
//            'scale' => $chords_scale,
//            'key' => $key,
//            'chord' => $chord,
//            'author' => $author,
//            'album' => $album,
//            'energy' => $energy,
//        ]);

        return [$chords_scale, $energy, $bpm, $author, $key];
    }

    public function updateDuration(array|string $slug = null): array
    {
        if ($slug === null) {
            $songs = Song::query()->whereNull('duration')->get();
        } else {
            $songs[] = Song::query()->where('slug', $slug)->first();
        }
        dump('found ' . count($songs) . ' songs');
        $completed = [];
        /** @var Song $song */
        foreach ($songs as $song) {
            // check if $image Already exists
            $this->getSongImage($song);

            // get song duration from path using getID3
            $songPath = $song->path;
            $fileInfo = $this->getAnalyze($songPath);
            try {
                $duration = $fileInfo['playtime_seconds'];
                //$play_time = $fileInfo['playtime_string'];
                $song->duration = $duration;
                $song->save();
            } catch (\Exception $e) {
                dump($e->getMessage());
                $this->getSongDetailsFromSoundCloud($song);
                continue;
            }
            // get info from id3v2 tags
            try {
                $this->getInfoFromId3v2Tags($fileInfo, $song);
                $song->save();
            } catch (\Exception $e) {
                dump($e->getMessage());
                continue;
            }

            try {
                $this->setImageFromSong($fileInfo['comments']['picture'][0]['data'], $song);
                $song->save();
            } catch (\Exception $e) {
                $this->getSongDetailsFromSoundCloud($song);
                dump($e->getMessage());
                continue;
            }

            $completed[] = [
                'title' => $song->title,
                'author' => $song->author,
                'duration' => $song->duration,
                'slug' => $song->slug,
                'image' => $song->image,
            ];
        }

        return $completed;
    }

    /**
     * @param $data
     * @param Song|null $song
     * @return void
     */
    public function setImageFromSong($data, Song|null $song): void
    {
        // get image from picture data in comments tag
        $image = $data;
        // save image to storage/app/public/images/
        $imageName = $song->slug . '.jpg';
        $imagePath = "storage/app/public/images/$imageName";
        file_put_contents($imagePath, $image);
        $imagePath = 'http://mage.tech:8899' . "/storage/images/$imageName";

        if ($song->image === null) {
            $song->image = $imagePath;
        }
    }

    /**
     * @param mixed $songPath
     * @return array
     */
    public function getAnalyze(mixed $songPath): array
    {
        $path = str_replace('http://mage.tech:8899/storage/', '', $songPath);
        $getID3 = new \getID3;
        $fileInfo = $getID3->analyze("storage/app/public/$path");
        return $fileInfo;
    }

    /**
     * @param $fileInfo
     * @param Song|null $song
     * @return void
     */
    public function getInfoFromId3v2Tags($fileInfo, Song|null $song): void
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

            return;
        }
        $genres = $idv['genre'] ?? $song->genre;
        $artist = $idv['artist'] ?? $song->author;
        $title = $idv['title'][0] ?? $song->title;
        // remove everything after the | in the title
        if (str_contains($title, '|')) {
            $title = substr($title, 0, strpos($title, '|'));
        }
        $title = trim($title);
        // replace spaces with underscores in the title
        // $title = str_replace(' ', '_', $title);

        $comment = $idv['comment'][0] ?? $song->comment;
        if ($song->genre === null) {
            $song->genre = $genres;
        }
        if ($song->author === null) {
            $song->author = $artist;
        }
        $song->title = $title;
        $song->comment = $comment;
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
        dump("call soundcloud download command scrape:sc");
        // call soundcloud download command scrape:sc
        Artisan::call('scrape:sc', [
            'link' => $link
        ]);
       // Artisan::call('song:import');

        $slug = Str::slug($songTitle, '_');

        if ($slug === '' ){
            return;
        }
        $checkSong = Song::query()->where('slug', 'like', "%$slug%")->get(['id','title', 'slug', 'author', 'image'])->toArray();
    }

    /**
     * @param string $file
     * @return string|null
     */
    public function getExistingImageFromFile(string $file, string $slug = null): string | null
    {
        $imageName = str_replace('mp3', 'jpg', $file);
        $imageName = str_replace('audio/', 'images/', $imageName);
        $imagePath = Storage::path("public/$imageName");
        $altImage = str_replace('.', '', $file);
        $altImage = str_replace('audio/', 'images/', $altImage);
        $altImage = $altImage . '.jpeg';
        $altImagePath = Storage::path("public/$altImage");

        if (file_exists($imagePath)) {
            return 'http://mage.tech:8899/' ."storage/" .  $imageName;
        }elseif (file_exists($altImagePath)){
            return 'http://mage.tech:8899/' ."storage/" .  $altImage;
        }
        return null;
    }

    /**
     * @param Song $song
     * @return Song
     */
    public function getSongImage(Song $song): Song
    {
        $file = $this->getFilePath($song);
        $image = $song->image;
        if ($image == null || (int)$image == 0) {
            $imagePath = $this->getExistingImageFromFile($file, $song->slug);
            if(!$imagePath) {
                $this->getSongDetailsFromSoundCloud($song);
            }
            $song->image = $imagePath;
            $song->save();
        }
        return $song;
    }
}
