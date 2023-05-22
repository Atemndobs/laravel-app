<?php

namespace App\Services;

use App\Exceptions\SongException\NotAnalyzedException;
use App\Jobs\ClassifySongJob;
use App\Models\Song;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;

class ClassifyService
{
    protected Song $song;

    protected array $songs;

    /**
     * @param  string  $track
     * @return array|string[]
     */
    public function analyzeTrack(string $track)
    {
        if ($this->checkSong($track)) {
            return [
                'status' => $this->song->status,
                'key' => $this->song->key,
                'bpm' => $this->song->bpm,
                'scale' => $this->song->scale,
                'danceability' => $this->song->danceability,
                'happy' => $this->song->happy,
                'sad' => $this->song->sad,
                'analyzed' => $this->song->analyzed,
            ];
        }
        ClassifySongJob::dispatch($track);

        return [
            'status' => 'qeued',
            'track' => $track,
        ];
    }

    /**
     * @param  string  $title
     * @return bool
     */
    public function checkSong(string $title): bool
    {
        $existingSong = $this->checExistingSong($title);
        if ($existingSong->key != null && $existingSong->bpm != null) {
            $this->song = $existingSong;

            return true;
        }

        return false;
    }

    /**
     * @param  string  $title
     * @return array|Song
     */
    public function checExistingSong(string $title): array | Song
    {
        $existingSong = Song::where('title', '=', $title)->first();
        if ($existingSong == null) {
            throw new ModelNotFoundException("$title does not exist , Please upload and try again");
        }

        return $existingSong;
    }

    /**
     * @param  array|string|null  $slug
     * @return array|Song
     *
     * @throws NotAnalyzedException
     */
    public function reClassify(array|string|null $slug = null): Song|array
    {
        if ($slug == null) {
            // exit no slug provided
            return [
                'status' => 'No Slug Provided',
            ];
        } else {
            $songs = Song::where('slug', '=', $slug)->get();
            if ($songs->count() == 0) {
                throw new ModelNotFoundException("$slug does not exist , Please upload and try again");
            }
            if ($songs->count() > 1) {
                throw new ModelNotFoundException("$slug is not unique , Please upload and try again");
            }
            $song = $songs->first();
            $analyzed = DB::table('songs')->where('slug', '=', $slug)->first()->analyzed;
            if (! $analyzed) {
                $message = "Song $slug is not analyzed yet, please analyze it first";
                throw new NotAnalyzedException($message);
            }
            /** @var Song $song */
            if ($song->status == 're-classified' || $song->classification_properties != null) {
                $message = "Song $slug is already classified";
                Log::info($message);
                return [$this->buildResponse($song->toArray(), $song)];
            }
        }

        return $this->buildClassificaton($song);
    }

    /**
     * @param  array  $savedSong
     * @param  Song  $song
     * @return array
     */
    #[ArrayShape(['slug' => 'mixed', 'classification_properties' => 'false|string', 'values' => 'false|string'])]
    public function buildResponse(array $savedSong, Song $song): array
    {
        return [
            'slug' => $savedSong['slug'],
            'classification_properties' => json_encode($savedSong['classification_properties']),
            'values' => json_encode(
                [
                    'moood_happy' => $song['happy'],
                    'moood_sad' => $song['sad'],
                    'energy' => $song['energy'],
                    'danceability' => $song['danceability'],
                    'relaxed' => $song['relaxed'],
                ]
            ),
        ];
    }

    /**
     * @param Song $song
     * @return array
     */
    public function buildClassificaton(Song $song): array
    {
        // create classification properties array with emotion, danceability, aggressiveness, energy,
        // if mood_happy is > 0.5 emotion is happy, else sad
        // if danceability > 50%, danceability is true, if danceability < 50%, danceability is false
        // if energy > 50%, energy is high, if energy < 50%, energy is low
        // if relaxed > 50%, relaxed is true, if relaxed < 50%, relaxed is false
        /** @var Song $song */
        $classification_properties = [
            'mood' => $song->happy > 0.5 ? 'happy' : 'sad',
            'energy' => $song->energy > 0.5 ? 'high' : 'low',
            'danceability' => $song->danceability > 0.5,
            'relaxed' => $song->relaxed > 0.5,
        ];
        $song->status = 're-classified';
        $song->classification_properties = $classification_properties;
        $song->save();
        $savedSong = $song->toArray();
        return $this->buildResponse($savedSong, $song);
    }
}
