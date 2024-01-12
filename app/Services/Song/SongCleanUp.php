<?php

namespace App\Services\Song;
use App\Models\Song;
use App\Models\SongKey;
use Illuminate\Support\Facades\Log;
use Laravel\Scout\Scout;

/**
 * Class SongCleanUp
 * @package App\Services\Song
 */
class SongCleanUp
{
    /**
     * @param array $song
     * @return array
     */
    public function CleanUpSingleSong(array $song): array
    {
        $song = $this->setNumericKey($song);
        // Exclude unnecessary attributes
        unset($song['created_at']);
        unset($song['updated_at']);
        unset($song['deleted_at']);
        unset($song['analyzed']);
        unset($song['played']);
        unset($song['song_url']);
        unset($song['genre']);
        unset($song['slug']);
        unset($song['path']);
        unset($song['image']);
        unset($song['comment']);
        unset($song['status']);
        unset($song['classification_properties']);
        unset($song['related_songs']);
        unset($song['title']);
        unset($song['author']);
        unset($song['sad']);
        unset($song['happy']);
        unset($song['danceability']);
        unset($song['relaxed']);
        unset($song['aggressiveness']);
        unset($song['energy']);
        unset($song['id']);
        Log::info(json_encode($song, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return $song;
    }
    public function CleanupAllSongs(): array
    {
        $allSongs = (new SearchSong())->getSongs()['hits'];
        $cleanedUpSongs = [];
        foreach ($allSongs as $song) {
            $cleanedUpSongs[] =  $this->cleanUpSingleSong($song);
        }
        return $cleanedUpSongs;
    }

    private function setNumericKey(array $song) : array
    {
        $songKeys = SongKey::all();
        $songScale = ucfirst($song['scale']);
        try {
            $songKey = $songKeys->where('key_name', $song['key'])
                ->where('scale', $songScale)
                ->first()->key_number;
        }catch (\Exception $e){
            $message = [
                'error' => 'Song key not found',
                'key' => $song['key'],
                'scale' => $songScale,
                //'song' => $song,
                'songKeys' => $songKeys->toArray(),
            ];
           throw new \Exception(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        $song['key'] = $songKey;
        $song['scale'] = $song['scale'] === "major" ? 1 : 0;

        $updatedAttributes = [
            'key' => $songKey,
            'scale' => $song['scale'],
        ];
        Log::info(json_encode($updatedAttributes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return $song;
    }
}