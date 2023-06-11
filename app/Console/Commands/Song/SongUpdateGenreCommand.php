<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use App\Services\Birdy\SpotifyService;
use App\Services\Scraper\SoundcloudService;
use App\Services\Song\GenreUpdateService;
use App\Services\SongUpdateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SongUpdateGenreCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:genre {author?} {--s|slug=}  {--g|genre=} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Artist Genre from Spotify';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $genres = [];
        $spotifyService = new SpotifyService();
        $author = $this->argument('author');
        $slug = $this->option('slug');
        $genre = $this->option('genre');
        if ($genre !== null) {
            $this->updateSongsWithGenre($genre);
        }
        if ($slug !== null) {
            $this->info('Update Genre for song with slug: '.$slug);
            $song = Song::query()->where('slug','like', "%$slug%")->first();
            $genreService = new GenreUpdateService();
            /** @var Song $song */
            $genres = $genreService->getGenreFromId3($song)->genre;
            $genres = json_encode($genres);
            $this->line("<fg=magenta> Genres | $genres </>");
            return 0;
        }

        $song = Song::query()->where('author', 'like', "%$author%")->first(
        //  ['author', 'title', 'genre']
        );
        if (strlen($author) > 0 || $author !== null) {
            /** @var Song $song */
            $genres = $spotifyService->getGenreByArtist($author, $song);
            if (count($genres) === 0) {
                $this->info('No genres found');

                return 0;
            }


            if ($song === null) {
                $song = Song::query()->where('author', 'like', "%$author%")->first();

            }
            if ($song->genre !== null) {
                $gen = json_encode($song->genre);
                $title = $song->title;
                $this->info("$title by  $author : $gen");
                return 0;
            }
            $song->genre = $genres;

            $song->save();
            $displayGenres = json_encode($genres);
            $this->output->info("$author : $displayGenres");

            return 0;
        }

        // get genres from getId
        $this->getSongGenreFromId3();
        $songs =
            Song::query()->whereNull('genre')
                ->orWhere('genre', '=', 0)
                ->orWhere('genre', '=', '[]')
                ->orWhere('genre', '=', null)
                ->where('author', '!=', null)
                ->get();

        if (count($songs) === 0) {
            $this->output->info('song:genre | No more songs to update');
            ray('song:genre | No songs to update')->green();
            return 0;
        }
      // $this->info('Found '.count($songs).' songs to update from Spotify');


        return 0;
    }

    private function getSongGenreFromId3(): void
    {
        $songs = Song::query()->whereNull('genre')
            ->orWhere('genre', '=', 0)
            ->orWhere('genre', '=', '[]')
            ->orWhere('genre', '=', null)
            ->get();

        Log::warning("Found ".count($songs)." songs to update from ID3");

        if (count($songs) === 0) {
            $this->output->info('song:genre | No songs to update from ID3');
            ray('song:genre | No songs to update')->green();
        }
        $this->info('Found '.count($songs).' songs to update For ID3');
        $genreService = new GenreUpdateService();
        /** @var Song $song */
        foreach ($songs as $song) {
                try {
                     $songGenre = $genreService->getGenreFromId3($song)->genre;
                     $genre = json_encode($songGenre);
                     $this->output->info("$song->title : $genre");
                 }catch (\Exception $e) {
                     $this->warn($e->getMessage());
                 }
                 $left = count(
                     Song::query()->whereNull('genre')
                     ->orWhere('genre', '=', 0)
                     ->orWhere('genre', '=', '[]')
                     ->orWhere('genre', '=', null)
                     ->get()
                 );
                 $this->line("<fg=red;bg=cyan>$left songs left</>");
        }

        $left = count(
            Song::query()->whereNull('genre')
                ->orWhere('genre', '=', 0)
                ->orWhere('genre', '=', '[]')
                ->orWhere('genre', '=', null)
                ->get()
        );

        if ($left >  0) {
            // redownload song from sound cloud and update genre
            $this->info('Found '.$left.' songs to update from SoundCloud');
            $this->info('Downloading songs from SoundCloud');
            $updateService = new SongUpdateService();
            foreach ($songs as $song) {
                $updateService->getSongDetailsFromSoundCloud($song);
            }
        }

    }

    private function updateSongsWithGenre(bool|array|string $genre)
    {
        $songs = Song::query()->where('title', 'like', "%$genre%")->get([
            'id',
            'title',
            'author',
            'genre',
        ]);
       // dd($songs->toArray());
        foreach ($songs as $song) {
            // add $genre to $song->genre array
            $existingGenre = $song->genre;
            if ($existingGenre === null || $existingGenre === '[]' || $existingGenre === '0' || $existingGenre === 0) {
                $existingGenre = [];
            }
            if (is_array($genre)) {
                $existingGenre = array_merge($existingGenre, $genre);
            } else {
                $existingGenre[] = $genre;
            }
           // $song->genre = $existingGenre;
           // $song->save();
        }

        return Song::query()->where('title', 'like', "%$genre%")->get([
            'id',
            'title',
            'author',
            'genre',
        ]);
    }

}
