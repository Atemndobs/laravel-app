<?php

namespace App\Services\Song;

use App\Models\Song;
use App\Services\Birdy\SpotifyService;
use App\Services\Scraper\SoundcloudService;
use App\Services\SongUpdateService;
use Illuminate\Support\Facades\Log;
use function Amp\call;
use const Widmogrod\Monad\IO\tryCatch;

class GenreUpdateService
{
    public function getGenreFromId3(Song $song) : Song
    {
        try {
            $this->updateRemixedSongs($song);
        }catch (\Exception $e) {
            $message = [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ];
           Log::error(json_encode($message, JSON_PRETTY_PRINT));
           dump($message);
        }
        $id3Service = new SongUpdateService();
        $spotify = new SpotifyService();
        if ($song->genre != null) {
            return $song;
        }
        $songPath = $id3Service->getFilePath($song);

        $fileInfo = $id3Service->getAnalyze($songPath);
        $id3Service->getInfoFromId3v2Tags($fileInfo, $song);
        $song->save();


        if (strlen($song->title) < 1 && strlen($song->author) < 1) {
            // remove mp3 from $song->slug
            $slug = str_replace('mp3', '', $song->slug);
            $slug = str_replace('_', ' ', $slug);

            $track = $spotify->getGenreFromSong($slug);;
            $song->title = $track['title'];
           $artist= $track['author'];
            if ($song->author == "" || $song->author == null) {
                // if $artist is array, get all values and implode
                if (is_array($artist)) {
                    $artist = implode(',', $artist);
                }
                $song->author = $artist;
                $song->save();
            }
            $song->genre = $track['genre'];
            $song->save();
            return $song;
        }
        if ((int)$song->genre == 0) {
            $artist = $song->author;
            if (strlen($artist) < 1) {
                try {
                    $artist = $fileInfo['tags']['id3v2'] ['artist'][0];
                    if ($song->author == "" || $song->author == null) {
                        // if $artist is array, get all values and implode
                        if (is_array($artist)) {
                            $artist = implode(',', $artist);
                        }
                        $song->author = $artist;
                        $song->save();
                    }
                    $song->save();
                }catch (\Exception $e) {
                    $message = [
                        'song' => [
                            'title' => $song->title,
                            'author' => $song->author,
                            'genre' => $song->genre,
                            'path' => $song->path,
                            'slug' => $song->slug,
                        ],
                        'message' => $e->getMessage(),
                        'next step' => 'We shall try to get genre from spotify',
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                        'class & method' => __CLASS__ . ' ' . __METHOD__,
                    ];
                    Log::error(json_encode($message, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
                    dump($message);
                    $slug = str_replace('mp3', '', $song->slug);
                    $slug = str_replace('_', ' ', $slug);
                    // if song title is empty, get title from slug
                    if (strlen($song->title) < 1) {
                        $song->title = str_replace('/', '', $song->title);
                        $song->save();
                    }

                    // if gerne is empty, get genre from spotify
                    if ($song->genre == null || $song->genre == '[]' || $song->genre == '0' || $song->genre == 0 || count($song->genre) < 1) {
                        $slug = substr($slug, 0, strrpos($slug, ' '));
                        $genre = $spotify->getArtistGenre($slug);
                        $song->genre = $genre;
                        $song->save();
                    }

                    return $song;
                }
            }
        }
        try {
            $tags_html = $fileInfo['tags_html'];
            if ($tags_html){
                if ($fileInfo['tags_html']['id3v2']){
                    try {
                        if ($fileInfo['tags_html']['id3v2']['genre']){
                            $genre = $fileInfo['tags_html']['id3v2']['genre'][0];
                            // decode $genre
                            $genre = html_entity_decode($genre);
                            $song->genre = $genre;
                            $song->save();
                            return $song;
                        }
                    }catch (\Exception $e) {
                        $message = [
                            'song' => [
                                'title' => $song->title,
                                'author' => $song->author,
                                'genre' => $song->genre,
                                'path' => $song->path,
                                'slug' => $song->slug,
                            ],
                            'message' => $e->getMessage(),
                            'next step' => 'We have failed to get genre from id3v2 tags, We need to get the genre by other means',
                            'idv3 tags' => $fileInfo['tags_html']['id3v2'],
                            'line' => $e->getLine(),
                            'file' => $e->getFile(),
                            'class & method' => __CLASS__ . ' ' . __METHOD__,
                        ];
                        Log::error(json_encode($message, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
                        dump($message);
                    }
                }
            }
        }catch (\Exception $e) {
            $message = [
                'song' => [
                    'title' => $song->title,
                    'author' => $song->author,
                    'genre' => $song->genre,
                    'path' => $song->path,
                    'slug' => $song->slug,
                ],
                'message' => $e->getMessage(),
                'next step' => 'HTML Tags are empty',
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'class & method' => __CLASS__ . ' ' . __METHOD__,
                'file_info' => $fileInfo
            ];
            Log::error(json_encode($message, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
            dump($message);
        }
        $genre = $spotify->getArtistGenre($song->author);
        $song->genre = $genre;
        $song->save();

        return $song;
    }

    private function updateRemixedSongs(\Illuminate\Database\Eloquent\Builder|Song $song): void
    {
        $existingGenre = $song->genre;
        // if genre is string exit
        if (is_string($existingGenre)) {
            return;
        }
        if (!is_array($existingGenre)) {
            return;
        }
        if ($existingGenre === null || $existingGenre === '[]' || $existingGenre === '0' || $existingGenre === 0 || count($existingGenre) < 1)  {
            if (str_contains($song->title, 'amapiano')){
                $existingGenre[] = 'amapiano';
            }
            if (str_contains($song->title, 'afrobeat')){
                $existingGenre[] = 'afrobeat';
            }
            if (str_contains($song->title, 'Moombahton')){
                $existingGenre[] = 'Moombahton';
            }
            if (str_contains($song->title, 'Remix')){
                $existingGenre[] = 'Remix';
            }
            if (str_contains($song->title, 'Dancehall')){
                $existingGenre[] = 'Dancehall';
            }
            $song->genre = $existingGenre;
            $song->save();
        }
    }
}
