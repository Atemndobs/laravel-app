<?php

namespace App\Console\Commands\Song;

use Illuminate\Console\Command;

class CollectGenres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:collect-genres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $songs = \App\Models\Song::all();
        // collect all song genres in an array, remove duplicates, export as json and csv files, and save to storage/app/genres
        // save the genres in the genres table
        $genres = [];

        /** @var \App\Models\Song $song */
        foreach ($songs as $song) {
            $genre = $song->genre;
            if ($genre == null) {
                continue;
            }
           // genre is an array, extract the genre names and add them to the genres array
            if (is_array($genre)) {
                foreach ($genre as $genreName) {
                    $genres[] = $genreName;
                }
            } else {
                $genres[] = $genre;
            }
        }
        // remove duplicates
        $genres = array_unique($genres);
        // sort the array
        sort($genres);
          // export as json and csv files, and save to storage/app/genres
        $genresJson = json_encode($genres, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $genresCsv = implode(',', $genres);
        $genresCsv = str_replace(',', "\n", $genresCsv);
        // save csv and json files to storage/app/genres
        $genresJsonFile = storage_path('app/genres/genres.json');
        $genresCsvFile = storage_path('app/genres/genres.csv');
        // create the genres directory if it doesn't exist
        if (!file_exists(storage_path('app/genres'))) {
            mkdir(storage_path('app/genres'));
        }
        file_put_contents($genresJsonFile, $genresJson);
        file_put_contents($genresCsvFile, $genresCsv);
        // output genres in a table with columns ID and Genre where ID is the index of the genre in the array
        $headers = ['ID', 'Genre'];
        $genres = array_values($genres);
        $genres = array_map(function ($genre, $index) {
            return [$index, $genre];
        }, $genres, array_keys($genres));
        $this->table($headers, $genres);
    }
}
