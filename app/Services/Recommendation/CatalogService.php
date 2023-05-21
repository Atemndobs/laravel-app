<?php

namespace App\Services\Recommendation;

use App\Models\Catalog;
use App\Models\Song;
use App\Models\User;
use Illuminate\Support\Arr;

class CatalogService
{
    /**
     * @param  int  $id
     * @return array
     */
    public function convertSongToCatalog(int $id): array
    {
        $song = Song::find($id);
        if ($song->analyzed === null || $song->analyzed === 0) {
            ray("not analyzed | $song->slug");
            dump("not analyzed | $song->slug");

            return [];
        }
        // check if song is already in catalog
        $catalog = Catalog::where('id', $song->id)->first();
        if ($catalog) {
            return [
                'id' => $catalog->id,
                'item_name' => $catalog->item_name,
                'item_category' => $catalog->item_category,
                'description' => $catalog->description,
                //'features_list' => $catalog->features_list,
            ];
        }
        $catalog = new Catalog();
        $catalog->item_name = $song->title;
        //  $catalog->item_category = 'song';
        $catalog->item_category = Arr::first($song->genre) ?? 'unknown';

        $catalog->description = $song->slug;
        $catalog->features_list = implode(',', [
            "bpm=$song->bpm", "key=$song->key", "scale=$song->scale", "energy=$song->energy",
            "happy=$song->happy", "sad=$song->sad", "aggressiveness=$song->aggressiveness",
            "danceability=$song->danceability", "relaxed=$song->relaxed",
        ]);

        // remove quotes from features_list
        $catalog->features_list = str_replace('"', '', $catalog->features_list);

        $catalog->save();

        return [
            'id' => $catalog->id,
            //  'item_id' => $catalog->item_id,
            'item_name' => $catalog->item_name,
            'item_category' => $catalog->item_category,
            'description' => $catalog->description,
            //'features_list' => $catalog->features_list,
        ];
    }

    /**
     * @return array
     */
    public function creatCatalog(): array
    {
        $catalogs = [];
        foreach (Song::all() as $song) {
            $catalogs[] = $this->convertSongToCatalog($song->id);
        }

        return $catalogs;
    }

    // export catalog to csv file

    /**
     * @param  string|null  $file_name
     * @return array
     */
    public function exportCatalog(?string $file_name = 'catalog.csv'): array
    {
        $catalogs = [];
        foreach (Catalog::all() as $catalog) {
            $catalogs[] = [
                'id' => $catalog->id,
                'item_name' => $catalog->item_name,
                'item_category' => $catalog->item_category,
                'description' => $catalog->description,
                'features_list' => $catalog->features_list,
            ];
        }
        if (! $file_name) {
            $file_name = 'catalog.csv';
        }
        $file = fopen($file_name, 'w');
        // fputcsv($file, ['id', 'item_name', 'item_category', 'description', 'features_list']);

        foreach ($catalogs as $catalog) {
            // convert catalog to string
            $catalog = implode(',', $catalog);
            $catalog = str_replace('"', '', $catalog);
            fputcsv($file, [$catalog]);
        }
        fclose($file);

        return $catalogs;
    }

    /**
     * @return array
     */
    public function createUsageData()
    {
        $users = User::all();
        $catalogs = Catalog::all();

        // create txt file for catalog ids and user ids
        $file = fopen('song_usage.txt', 'w');
        foreach ($users as $user) {
            foreach ($catalogs as $catalog) {
                fputcsv($file, [$user->id, $catalog->id]);
            }
        }
        fclose($file);

        return $catalogs->toArray();
    }
}
