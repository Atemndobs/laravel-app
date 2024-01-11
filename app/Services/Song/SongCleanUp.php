<?php

namespace App\Services\Song;
use App\Models\Song;
use App\Models\SongKey;
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
        $songKeys = SongKey::all();
        foreach ($songKeys as $songKey) {
            if ($song['key'] === $songKey->key_name) {
                $song['key'] = $songKey->key_number;
                break;
            }
        }

       // dump($song['key']);
        $song['scale'] = $song['scale'] === "major" ? 1 : 0;
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
//        unset($song['sad']);
//        unset($song['happy']);
//        unset($song['danceability']);
//        unset($song['relaxed']);
//        unset($song['aggressiveness']);

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
}