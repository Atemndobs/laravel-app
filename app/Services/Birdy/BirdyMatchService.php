<?php

namespace App\Services\Birdy;

use App\Models\Song;
use Illuminate\Support\Facades\Log;
use Laravel\Octane\Exceptions\DdException;
use MeiliSearch\Endpoints\Indexes;
use MeiliSearch\Search\SearchResult;
use function PHPUnit\Framework\isEmpty;

class BirdyMatchService
{
    public MeiliSearchService $meiliSearchService;
    private Indexes $songIndex;
    public array $playedSongs = [];
    public string $mood = 'happy';

    public function __construct()
    {
        $this->meiliSearchService = new MeiliSearchService();
        $this->songIndex = $this->meiliSearchService->getSongIndex();
    }

    /**
     * @param string $slug
     * @param string|null $key
     * @param string|null $mood
     * @param float|null $bpm
     * @param float|null $bpmMin
     * @param float|null $bpmMax
     * @param float|null $happy
     * @param float|null $sad
     * @param float|null $energy
     * @param float|null $danceability
     * @param float|null $bpmRange
     * @param int|null $id
     * @return array
     * @throws DdException
     */
    public function getSongMatch(
        string $slug,
        string | null $key,
        string | null $mood,
        float | null $bpm,
        float | null $bpmMin,
        float | null $bpmMax,
        float | null $happy,
        float | null $sad,
        float | null $energy,
        float | null $danceability,
        float | null $bpmRange,
        int | null $id,
        int | null $limit = 100
    ): array
    {
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
        $songMatchCriteria = new MatchCriteriaService();
        $criteria = $songMatchCriteria->getCriteria();
        $bpmRange = $criteria->bmp_range ?? $bpmRange;
        if ($id)
            $criteria->addPlayedSongs($id);

        $vibe = $this->getSimilarSongWithExactBpm($song,  $limit);
        if ($vibe->getHitsCount() < 3) {
            $vibe = $this->relaxSearchFilters($vibe, $song, $bpmRange);
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
     * @param Song $incomingSong
     * @param string $attribute
     * @return array
     */
    public function getAttributeMatch(Song $incomingSong, string $attribute): array
    {
        $matches = [];
        $songs = Song::all();

        /** @var Song $song */
        foreach ($songs as $song) {
            if (is_float($incomingSong->{$attribute})) {
                $songAttribute = $this->roundNumbers($song->{$attribute});
                $incomingSongAttribute = $this->roundNumbers($incomingSong->{$attribute});
            } else {
                $songAttribute = $song->{$attribute};
                $incomingSongAttribute = $incomingSong->{$attribute};
            }
            if ($songAttribute == $incomingSongAttribute) {
                $matches['query'] = $attribute;
                $matches[$attribute] = $incomingSongAttribute;
                $matches['id'] = $song->id;
                $matches['path'] = $song->path;
            }
        }

        return $matches;
    }

    public function roundNumbers(float $float): float|int
    {
        return round($float * 2) / 2;
    }

    /**
     * @param string $attribute
     * @param float|string $value
     * @param float $range
     * @return array|array[]|SearchResult|\mixed[][]
     */
    public function searchByAttribute(string $attribute, float|string $value, float $range = 1): array|SearchResult
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
    protected function getByBpm(float|string $value, float $range, string $attribute): array
    {
        $direction = 'asc';
        return $this->filterAndSort($value, $range, $attribute, $direction);
    }

    /**
     * @param float|string $value
     * @param float|int $range
     * @param string $attribute
     * @param string $direction
     * @return array|SearchResult
     */
    protected function filterAndSort(
        float|string $value,
        float|int $range,
        string $attribute,
        string $direction
    ): array | SearchResult {
        $min = $value - $range;
        $max = $value + $range;

        return $this->songIndex->search('', [
            'filter' => ["$attribute >= $min AND $attribute <= $max"],
            'sort' => ["$attribute:$direction"],
        ]);
    }

    /**
     * @param  float  $range
     * @param  float|string  $value
     * @param  string  $attribute
     * @return array[]|\mixed[][]
     */
    protected function getBySingleMood(float $range, float|string $value, string $attribute): array
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
     * @param string $attribute
     * @param string $keyValue
     * @param string $scaleValue
     * @return array|SearchResult
     */
    protected function getByKey(string $attribute, string $keyValue, string $scaleValue = 'major'): array | SearchResult
    {
        $direction = 'asc';
        $key = $attribute;

        return $this->songIndex->search('', [
            'filter' => ["$key = $keyValue", "scale = $scaleValue"],
            'sort' => ["$attribute:$direction"],
        ]);
    }

    /**
     * @param Song $song
     * @param float $bpmRange
     * @param float $moodRange
     * @param array $attributes
     * @return array|SearchResult
     */
    protected function getSimilarSong(
        Song $song,
        float $bpmRange = 1.0,
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
            if ($attribute === 'slug') {
                continue;
            }
            if ($attribute === 'title') {
                continue;
            }
            if ($attribute === 'author') {
                continue;
            }
            if ($attribute === 'bpm' ) {
                $min = $value - $bpmRange;
                $max = $value + $bpmRange;
               // $filter[] = "$attribute = $value";
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
                //dump($value, gettype($value));
                if (is_array($value)) {
                    $val = implode(',', $value);
                    $filter[] = "$attribute = '$val'";
                }
                if (is_string($value)) {
                    $filter[] = "$attribute = '$value'";
                }
                if (is_int($value)) {
                    $filter[] = "$attribute = $value";
                }
            }
        }
        $filter[] = "slug != '{$song->slug}'";
        $filter[] = 'analyzed = 1';
        $filter[] = 'energy >= 0';
        // remove songs with ids from the playedSongs array
       // $filter[] = "NOT id IN ";
        // genre is an array so we need to use the IN operator
        // $filter[] = "genre IN '$genre'";
       // $filter[] = "key = '$searchKey'";
        $direction = 'asc';

        if ((int)$attribute === 0){
            $attribute = 'bpm';
        }
        Log::info('filter__________________________________');
        Log::info((json_encode($filter, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)));
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
        $bpmMatches = $this->getAttributeMatch($incommingSong, 'bpm');
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
            $this->getAttributeMatch($song, 'bpm'),
            $this->getAttributeMatch($song, 'key'),
            $this->getAttributeMatch($song, 'scale'),
            $this->getAttributeMatch($song, 'happy'),
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

    public function getNextBestMatch(Song $song, string $bpmRange)
    {
        $attr = [
            'bpm',
            'key',
            'scale',
        ];
        $range = $bpmRange + 1;

        $res = $this->getSimilarSong($song, $range, 100, $attr);

        if ($res->getHitsCount() < 3) {
            $this->relaxSearchFilters($res, $song, $range);
        }

        return $res;
    }

    /**
     * @param SearchResult|array $searchResult
     * @param Song $song
     * @param float $bpmRange
     * @return array|SearchResult
     */
    public function relaxSearchFilters(SearchResult|array $searchResult, Song $song, float $bpmRange = 2): array | SearchResult
    {
        $attr = [
            'bpm',
            'key',
            'scale',
        ];

        $maxBpm = $this->getMaxBpm();

        while ($searchResult->getHitsCount() < 3) {
            $bpmRange = $bpmRange + 1;

            if ($bpmRange >= $maxBpm) {
                break;
            }
            $searchResult = $this->getSimilarSong($song, $bpmRange, 100, $attr);
        }
        // check of resulting songs were already in played songs
        $songMatchCriteria = new MatchCriteriaService();
        $criteria = $songMatchCriteria->getCriteria();
        $playedSongs = explode(',', $criteria->played_songs);

        if (! empty($playedSongs)) {
            $newVibe = $this->removePlayedSong($searchResult->getHits(), $playedSongs);
        }

        if (empty($newVibe)) {
            // relax search even more
            $bpmRange = $bpmRange + 1;
            $searchResult = $this->getSimilarSong($song, $bpmRange, 100, $attr, $playedSongs);
        }

        /** @var SearchResult|array $searchResult2 */
        $searchResult2 = [];

        if ($searchResult->getHitsCount() < 3) {
            while ($searchResult->getHitsCount() < 3) {
                $bpmRange = $bpmRange - 1;

                if ($maxBpm + $bpmRange <= 60) {
                    break;
                }
                $searchResult2 = $this->getSimilarSong($song, $bpmRange, 100, $attr);
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

    private function removePlayedSong(mixed $vibe, array $playedSongs)
    {
        foreach ($vibe as $key => $song) {
            if (in_array($song['id'], $playedSongs)) {
                unset($vibe[$key]);
            }
        }
        return $vibe;
    }

    private function getSimilarSongWithExactBpm(Song $song, int $limit)
    {
        $filter = [];
        $attributes = $this->songIndex->getFilterableAttributes();
        $this->setMood($attributes, $song);
        foreach ($attributes as $attribute) {
            $value = $song->{$attribute};
            if ($attribute === "analyzed" && $value === 0) {
                return [];
            }

            if (in_array($attribute, ['scale', 'key', 'genre', 'status', 'title', 'author', 'slug', 'sad', 'happy'])) {
                continue;
            }
            if ($attribute == 'bpm' ) {
                 $filter[] = "$attribute = $value";
            } elseif (is_float($value)) {
//                if ($value >= 0.5) {
//                    $moodMin = 0.5;
//                    $moodMax = 1;
//                }
//                if ($value < 0.5) {
//                    $moodMin = 0;
//                    $moodMax = 0.5;
//                }
//                $filter[] = "$attribute >= $moodMin AND $attribute <= $moodMax";
            }
        }

        if ($this->mood === 'happy') {
            $filter[] = "happy >= 0.5";
        }elseif ($this->mood === 'sad') {
            $filter[] = "sad >= 0.5";
        }


        $filter[] = 'analyzed = 1';

        // removable attributes
//        $filter[] = "key = '{$song->key}'";
//        $filter[] = "scale = '{$song->scale}'";
//        $genres = $song->genre;
//        foreach ($genres as $genre) {
//            $genreFilters[] = "genre = '{$genre}'";
//        }
//        $genreFilterQuery = '(' . implode(' OR ', $genreFilters) . ')';
//        $filter[] = $genreFilterQuery;

        // exclude played songs
        $songMatchCriteria = new MatchCriteriaService();
        $criteria = $songMatchCriteria->getCriteria();
        $playedSongs = explode(',', $criteria->played_songs);

        if (! empty($playedSongs)) {
            $playedSongsFilter = [];
            foreach ($playedSongs as $playedSong) {
                // get slug from song id
                $playedSong = Song::find($playedSong)->slug;
                $playedSongsFilter[] = "slug != '{$playedSong}'";
            }
            $playedSongsFilter[] = "slug != '{$song->slug}'";
            $playedSongsFilterQuery = '(' . implode(' AND ', $playedSongsFilter) . ')';
            $filter[] = $playedSongsFilterQuery;
        }else{
            $filter[] = "slug != '{$song->slug}'";
        }

        $direction = 'asc';
        Log::info('filter__________________________________');
        Log::info((json_encode($filter, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)));
        return $this->songIndex->search('', [
            'filter' => $filter,
            'sort' => ["$attribute:$direction"],
            'limit' => $limit,
            'offset' => 0,
        ]);
    }


    private function setMood(array $attributes, Song $song): void
    {
        $happy = 0;
        $sad = 0;
        foreach ($attributes as $attribute) {
            $value = $song->{$attribute};

            if ($attribute == 'happy') {
                $happy = $value;
            }
            if ($attribute == 'sad') {
                $sad = $value;
            }
        }
        if ($happy > $sad) {
            $this->mood = 'happy';
        }
        if ($sad > $happy) {
            $this->mood = 'sad';
        }
    }
}
