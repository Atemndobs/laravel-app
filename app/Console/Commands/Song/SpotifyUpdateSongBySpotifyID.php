<?php

namespace App\Console\Commands\Song;

use App\Services\Scraper\SpotifyMusicService;
use Illuminate\Console\Command;

class SpotifyUpdateSongBySpotifyID extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:spotify-update  {id} {--t|title=} {--a|author=} {--g|genre=} {--bpm=} {--i|image=} {--path=} {--id|song_id=} {--u|song_url=} {--source=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pass in a spotify id as song_id and ID of song, then indicate what to update (title of t, author or a, genre, bpm, image, path, song_id, song_url, source).
    Sample : php artisan song:spotify-update 7299 --song_id 6RB9YvNyP0RZfCUcMtZELH -i1 -g1  -a1 -ta or php artisan song:spotify-update 7299 --song_url https://open.spotify.com/track/6RB9YvNyP0RZfCUcMtZELH?si=fde0bdde82364690 -i1 -g1  -a1 -ta';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $title = $this->option('title');
        $author = $this->option('author');
        $genre = $this->option('genre');
        $bpm = $this->option('bpm');
        $image = $this->option('image');
        $path = $this->option('path');
        $song_id = $this->option('song_id');
        $song_url = $this->option('song_url');
        $source = $this->option('source');

        if (!$song_id && !$song_url) {
            $this->error('Please provide a song_id');
            return;
        }
        if (!$song_id && $song_url) {
            // get song_id from song_url (spotify track share url)
            $song_id = explode('/', $song_url)[4];
            $song_id = explode('?', $song_id)[0];
        }

        dump([
            'id' => $id,
            'title' => $title,
            'author' => $author,
            'genre' => $genre,
            'bpm' => $bpm,
            'image' => $image,
            'path' => $path,
            'song_id' => $song_id,
            'song_url' => $song_url,
            'source' => $source,

        ]);


        $spotifyService = new SpotifyMusicService();
        /**
         * @var \App\Models\Song $song
         */
        $song = \App\Models\Song::query()->find($id);

        if ($song) {

            if ($title) {
              // check if song has title, if yes return
                if ($song->title) {
                    $this->error('Song already has title:  ' . $song->title);
                }else{
                    // get title from spotify
                    $title = $spotifyService->getTitle($song_id);
                    $song->title = $title;
                }

            }
            if ($author) {
               // check if song has author, if yes return
                if ($song->author) {
                    $this->error('Song already has author:  ' . $song->author);
                }else{
                    // get author from spotify
                    $author = $spotifyService->getAuthor($song_id);
                    $song->author = $author;
                }
            }
            if ($genre) {
              //  get genre from spotify
                if ($song->genre) {
                    $this->error('Song already has genre');
                }else{
                    // get genre from spotify
                    $genre = $spotifyService->getGenre($song_id);
                    $song->genre = $genre;
                }
            }
            if ($image) {
                // check if song has imgage, if yes return
                if ($song->image) {
                    $this->error('Song already has image');
                }else{
                    // get image from spotify
                    $image = $spotifyService->getImage($song_id);
                    $song->image = $image;
                }

            }
            $song->save();
        }
        return;
    }
}
