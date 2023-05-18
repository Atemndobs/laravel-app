<?php

namespace App\Services\Birdy;

use App\Models\Song;
use Illuminate\Support\Facades\Log;
use MeiliSearch\Client;
use MeiliSearch\Endpoints\Indexes;
use MeiliSearch\Search\SearchResult;
use Stancl\Tenancy\Events\DatabaseDeleted;
use function example\int;
use function PHPUnit\Framework\isEmpty;

class BirdyMatchService
{
    public MeiliSearchService $meiliSearchService;
    private Indexes $songIndex;

    public function __construct()
    {
        $this->meiliSearchService = new MeiliSearchService();
        $this->songIndex = $this->meiliSearchService->getSongIndex();
    }

    /**
     * @param string $slug
     * @param float $bpm
     * @param float $bpmMin
     * @param float $bpmMax
     * @param float $happy
     * @param float $sad
     * @param string $key
     * @param float $energy
     * @param string $mood
     * @param float $danceability
     * @return array
     */
    public function getSongMatch(
        string $slug,
        string $key,
        string $mood,
        float $bpm,
        float $bpmMin,
        float $bpmMax,
        float $happy,
        float $sad,
        float $energy,
        float $danceability
    ): array
    {
        Log::info((
            [
                'slug' => $slug,
                'key' => $key,
                'mood' => $mood,
                'bpm' => $bpm,
                'bpmMin' => $bpmMin,
                'bpmMax' => $bpmMax,
                'happy' => $happy,
                'sad' => $sad,
                'energy' => $energy,
                'danceability' => $danceability,
            ]
        ));
        try {

            $song = $this->getExistingSong($slug);
        }catch (\Exception $e){
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
        if (! $this->checkAnalyzedSong($song)) {
            return ['status' => 'not analyzed'];
        }

        $vibe = $this->getSimmilarSong($song);

        Log::info($vibe->getHits());

        if ($vibe->getHitsCount() < 3) {
            $vibe = $this->relaxSearchFilters($vibe, $song);
        }

        return [
            'hits_count' => $vibe->getHitsCount(),
            'hits' => $vibe->getHits(),
        ];
    }

    /**
     * @param $slug
     * @return Song
     */
    public function getExistingSong($slug): Song
    {
        return Song::where('slug', '=', $slug)->first();
    }

    /**
     * @param  Song  $song
     * @return bool
     */
    public function checkAnalyzedSong(Song $song): bool
    {
        return (bool) $song->analyzed;
    }

    /**
     * @param Song $incommingSong
     * @param string $attribute
     */
    public function getAttributMatch(Song $incommingSong, string $attribute)
    {
        $matches = [];
        $songs = Song::all();

        /** @var Song $song */
        foreach ($songs as $song) {
            if (is_float($incommingSong->{$attribute})) {
                $songAttribute = $this->roundNumbers($song->{$attribute});
                $incommingSongAttribute = $this->roundNumbers($incommingSong->{$attribute});
            } else {
                $songAttribute = $song->{$attribute};
                $incommingSongAttribute = $incommingSong->{$attribute};
            }
            if ($songAttribute == $incommingSongAttribute) {
                $matches['query'] = $attribute;
                $matches[$attribute] = $incommingSongAttribute;
                $matches['id'] = $song->id;
                $matches['path'] = $song->path;
            }
        }

        return $matches;
    }

    public function roundNumbers(float $float)
    {
        return round($float * 2) / 2;
    }

    /**
     * @param  string  $attribute
     * @param  float|string  $value
     * @return void
     */
    public function searchByAttribute(string $attribute, float|string $value, float $range = 1)
    {
        if ($attribute === 'bpm') {
            return $this->getByBpm($value, $range, $attribute);
        } elseif (is_float($value)) {
            return $this->getBySingleMood($range, $value, $attribute);
        }

        return $this->getByKey($attribute, $value);
    }

    /**
     * @param  float|string  $value
     * @param  float  $range
     * @param  string  $attribute
     * @return array[]|\mixed[][]
     */
    protected function getByBpm(float|string $value, int $range, string $attribute): array
    {
        $dirrection = 'asc';

        return $this->filterAndSort($value, $range, $attribute, $dirrection);
    }

    /**
     * @param  float|string  $value
     * @param  float|int  $range
     * @param  Indexes  $songIndex
     * @param  string  $attribute
     * @param  string  $dirrection
     * @return array[]|\mixed[][]
     */
    protected function filterAndSort(
        float|string $value,
        float|int $range,
        string $attribute,
        string $dirrection
    ): array | SearchResult {
        $min = $value - $range;
        $max = $value + $range;

        return $this->songIndex->search('', [
            'filter' => ["$attribute >= $min AND $attribute <= $max"],
            'sort' => ["$attribute:$dirrection"],
        ]);
    }

    /**
     * @param  float  $range
     * @param  float|string  $value
     * @param  string  $attribute
     * @return array[]|\mixed[][]
     */
    protected function getBySingleMood(int $range, float|string $value, string $attribute): array
    {
        $possitives = [
            'energy',
            'happy',
            'aggressiveness',
            'danceability',
        ];

        $negatives = [
            'sad',
            'relaxed',
        ];

        $dirrection = 'asc';

        if (in_array($attribute, $possitives)) {
            $dirrection = 'asc';
        }
        if (in_array($attribute, $negatives)) {
            $dirrection = 'desc';
        }
        $range = $range / 100;

        return $this->filterAndSort($value, $range, $attribute, $dirrection);
    }

    /**
     * @param  string  $attribute
     * @param  string  $value
     * @return array
     */
    protected function getByKey(string $attribute, string $keyValue, string $scaleValue = 'major'): array | SearchResult
    {
        $dirrection = 'asc';
        $key = $attribute;

        return $this->songIndex->search('', [
            'filter' => ["$key = $keyValue", "scale = $scaleValue"],
            'sort' => ["$attribute:$dirrection"],
        ]);
    }

    /**
     * @param  Song  $song
     * @return array
     */
    protected function getSimmilarSong(
        Song $song,
        float $bpmRange = 3.0,
        float $moodRange = 20.0,
        array $attributes = []
    ): array | SearchResult {
        $filter = [];

        if (count($attributes) < 1) {
            $attributes = $this->songIndex->getFilterableAttributes();
        }



        foreach ($attributes as $attribute) {
            $value = $song->{$attribute};

            if ($attribute === 'energy') {
                continue;
            }
            // remove slug attribute
            if ($attribute === 'slug') {
                continue;
            }
            if ($attribute === 'bpm') {
                $min = $value - $bpmRange;
                $max = $value + $bpmRange;
                $filter[] = "$attribute >= $min AND $attribute <= $max";
            } elseif (is_float($value)) {
                $range = $moodRange / 100;
                $moodMin = $value - $range;
                $moodMax = $value + $range;
                if ($value < 1) {
                    $moodMin = 0;
                }
                $filter[] = "$attribute >= $moodMin AND $attribute <= $moodMax";
            } else {
                $val = strval($value);
           //     $filter[] = "$attribute = '$val'";
            }
        }
        // remove song with same slug as the song we are analyzing
        $filter[] = "slug != '{$song->slug}'";
        $filter[] = 'analyzed = 1';
        $filter[] = 'energy >= 0';
        $direction = 'asc';

        if ((int)$attribute === 0){
            $attribute = 'bpm';
        }

        Log::info(($filter));

        die();
        return $this->songIndex->search('', [
            'filter' => $filter,
            'sort' => ["$attribute:$direction"],
            'limit' => 10,
            'offset' => 0,
        ]);
    }

    public function defaltMatches(Song $incommingSong)
    {
        $id = $incommingSong->id;
        $bpmMatches = $this->getAttributMatch($incommingSong, 'bpm');
    }

    public function getBpmMatch(Song $incommingSong)
    {
        $matches = [];
        $songs = Song::all();

        /** @var Song $song */
        foreach ($songs as $song) {
            if ($this->roundNumbers($song->bpm) == $this->roundNumbers($incommingSong->bpm)) {
                $matches['id'] = $song->id;
                $matches['path'] = $song->path;
            }
        }

        return $matches;
    }

    /**
     * @param  Song  $song
     * @param  string  $attr
     * @return void
     */
    public function getMatchByAttribute(Song $song, string $attr = 'bpm')
    {
        $matches = [
            $this->getAttributMatch($song, 'bpm'),
            $this->getAttributMatch($song, 'key'),
            $this->getAttributMatch($song, 'scale'),
            $this->getAttributMatch($song, 'happy'),
        ];

        $response = [
            'query' => [
                'slug' => $song->slug,
                'bpm' => $this->roundNumbers($song->bpm),
                'path' => $song->path,
            ],
            'matches' => $matches,
        ];

        return $this->searchByAttribute($attr, $song->{$attr}, 2);
    }

    public function getNextBestMatch(Song $song)
    {
        $attr = [
            'bpm',
            'key',
            'scale',
        ];

        $res = $this->getSimmilarSong($song, 4, 100, $attr);

        if ($res->getHitsCount() < 3) {
            $this->relaxSearchFilters($res, $song);
        }

        return $res;
    }

    /**
     * @param  SearchResult|array  $searchResult
     * @param  Song  $song
     * @return array|SearchResult
     */
    public function relaxSearchFilters(SearchResult|array $searchResult, Song $song)
    {
        $attr = [
            'bpm',
            'key',
            'scale',
        ];
        $bpmRange = 4;

        $maxBpm = $this->getMaxBpm();

        while ($searchResult->getHitsCount() < 3) {
            $bpmRange = $bpmRange + 1;

            if ($bpmRange >= $maxBpm) {
                break;
            }
            $searchResult = $this->getSimmilarSong($song, $bpmRange, 100, $attr);
        }

        /** @var SearchResult|array $searchResult2 */
        $searchResult2 = [];

        if ($searchResult->getHitsCount() < 3) {
            while ($searchResult->getHitsCount() < 3) {
                $bpmRange = $bpmRange - 1;

                if ($maxBpm + $bpmRange <= 60) {
                    break;
                }
                $searchResult2 = $this->getSimmilarSong($song, $bpmRange, 100, $attr);
            }
        }

        if (! isEmpty($searchResult2) && $searchResult2->getHitsCount() > $searchResult->getHitsCount()) {
            return $searchResult2;
        }

        return $searchResult;
    }

    public function getMaxBpm()
    {
        return Song::max('bpm');
    }

    /**
     * @param  Song  $song
     * @return mixed
     */
    public function getGenre(Song $song)
    {
        $spotifyService = new SpotifyService($song);

        return $spotifyService->searchSong();
    }
}
