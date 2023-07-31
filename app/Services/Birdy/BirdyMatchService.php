<?php

namespace App\Services\Birdy;

use App\Models\MatchCriterion;
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
    public int $matchCount = 0;
    public int $excludedCount = 0;
    public array $options = [
        'bpm',
        'analyzed'
    ];

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
     * @param array|null $options
     * @param int|null $limit
     * @return array
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
        array | null $options,
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
        $this->excludedCount = $criteria->getPlayedSongsCount();

        // add options to  $this->options
        if ($options) {
            foreach ($options as $option) {
                if (! in_array($option, $this->options)) {
                    $this->options[] = $option;
                }
            }
        }

        $vibe = $this->getSimilarSongsWithOptions($song,  1000, $criteria);
        if ($vibe->getHitsCount() >= 3) {
            $this->matchCount = $vibe->getHitsCount();
            // return only $limit songs
            $results =  array_slice($vibe->getHits(), 0, $limit);
            return [
                'hits_count' => $this->matchCount,
                'hits' => $results,
                'match_count' => $this->matchCount,
                'excluded_count' => $this->excludedCount,
            ];
        }

        if ($vibe->getHitsCount() < 3) {
            $vibe = $this->relaxSearchFilters($vibe, $song, $bpmRange);
        }
        $this->matchCount = $vibe->getHitsCount();

        return [
            'hits_count' => $vibe->getHitsCount(),
            'hits' => $vibe->getHits(),
            'excluded_count' => $this->excludedCount,
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
        $positives = [
            'energy',
            'happy',
            'aggressiveness',
            'danceability',
        ];

        $negatives = [
            'sad',
            'relaxed',
        ];

        $direction = 'asc';

        if (in_array($attribute, $positives)) {
            $direction = 'asc';
        }
        if (in_array($attribute, $negatives)) {
            $direction = 'desc';
        }
        $range = $range / 100;

        return $this->filterAndSort($value, $range, $attribute, $direction);
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
        $direction = 'asc';

        Log::info('filter__________________________________');
        Log::info((json_encode($filter, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)));
        return $this->songIndex->search('', [
            'filter' => $filter,
            'sort' => ["$attribute:$direction"],
            'limit' => 10,
            'offset' => 0,
        ]);
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

    private function getSimilarSongsWithOptions(Song $song, int $limit, MatchCriterion $criteria)
    {
        $filter = [];
        $attributes = $this->songIndex->getFilterableAttributes();
        $this->setMood($song);
        foreach ($attributes as $attribute) {
            $value = $song->{$attribute};
            if ($attribute === "analyzed" && $value === 0) {
                return [];
            }
            // if $attribute is not in $this->options, skip it
            if (! in_array($attribute, $this->options)) {
                continue;
            }
            if ($attribute === 'genre') {
                $genres = $song->genre;
                $genreFilters = ["genre = ''"];
                foreach ($genres as $genre) {
                    $genreFilters[] = "genre = '{$genre}'";
                }

                $genreFilterQuery = '(' . implode(' OR ', $genreFilters) . ')';
                $filter[] = $genreFilterQuery;
                continue;
            }
            $filter[] = "$attribute = '{$value}'";

        }

        $filter = $this->addMoodToFilter($filter, $song, $criteria);
        $filter = $this->addSlugToFilter($criteria, $song, $filter);

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


    private function setMood(Song $song): void
    {
        $happy = $song->happy;
        $sad = $song->sad;
        if ($happy > $sad) {
            $this->mood = 'happy';
        }
        if ($sad > $happy) {
            $this->mood = 'sad';
        }
    }

    /**
     * @param MatchCriterion $criteria
     * @param Song $song
     * @param array $filter
     * @return array
     */
    public function addSlugToFilter(MatchCriterion $criteria, Song $song, array $filter): array
    {
        $playedSongs = explode(',', $criteria->played_songs);
        $playedSongs = array_filter($playedSongs);
        if (!empty($playedSongs)) {
            $playedSongsFilter = [];
            foreach ($playedSongs as $playedSong) {
                // get slug from song id
                $playedSong = Song::find($playedSong)->slug;
                $playedSongsFilter[] = "slug != '{$playedSong}'";
            }
            $playedSongsFilter[] = "slug != '{$song->slug}'";
            $playedSongsFilterQuery = '(' . implode(' AND ', $playedSongsFilter) . ')';
            $filter[] = $playedSongsFilterQuery;
        } else {
            $filter[] = "slug != '{$song->slug}'";
        }
        return $filter;
    }

    /**
     * @param array $filter
     * @param Song $song
     * @param MatchCriterion $criteria
     * @return array
     */
    public function addMoodToFilter(array $filter, Song $song, MatchCriterion $criteria): array
    {
        if (in_array('mood', $this->options)) {
            $mood = $this->mood;
            $value = $song->{$mood};
            $moodRange = $criteria->mood_range;
            $maxMood = $value + $moodRange;
            $minMood = $value - $moodRange;
            $filter[] = "$mood >= $minMood";
            $filter[] = "$mood <= $maxMood";
            return $filter;
        }
        if ($this->mood === 'happy') {
            $filter[] = "happy >= 0.5";
        } elseif ($this->mood === 'sad') {
            $filter[] = "sad >= 0.5";
        }
        return $filter;
    }
}
