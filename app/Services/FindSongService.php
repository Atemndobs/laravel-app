<?php

namespace App\Services;

use App\Models\Song;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class FindSongService
{
    public const ATTRIBUTES = [
        'id',
        'author',
        'title',
        'slug',
        'bpm',
        'key',
        'scale',
        'analyzed',
        'danceability',
        'energy',
        'happy',
        'path',
        'image'
    ];
    /**
     * @param  float  $bpm
     * @return array
     */
    public function findByBpm(float $bpm): array
    {
        $songs = $this->getAttribute('bpm', $bpm, 0);
        if ($songs->count() === 0) {
            $songs = $this->getAttribute('bpm', $bpm);
        }

        while ($songs->count() === 0) {
            $bpm = $bpm + 1;
            $songs = $this->getAttribute('bpm', $bpm);
        }

        return $songs->toArray() ?? [];
    }

    public function findByTitle(string $title): array
    {
        $songs = $this->getAttribute('title', $title);

        return $songs->toArray() ?? [];
    }

    public function findByslug(string $artist): array
    {
        $songs = $this->getAttribute('slug', $artist);

        return $songs->toArray() ?? [];
    }

    public function findByScale(string $scale): array
    {
        $songs = $this->getAttribute('scale', $scale);

        return $songs->toArray() ?? [];
    }

    public function findByKey(string $key): array
    {
        $songs = $this->getAttribute('key', $key);

        return $songs->toArray() ?? [];
    }

    /**
     * @param  string  $genre
     * @return array
     */
    public function findByGenre(string $genre): array
    {
        $songs = $this->getAttribute('genre', $genre);

        return $songs->toArray() ?? [];
    }

    // find soong by Image
    public function findByImage(string $image): array
    {
        $songs = $this->getAttribute('image', $image);

        return $songs->toArray() ?? [];
    }

    /**
     * @param $bpm
     * @param $scale
     * @param $artist
     * @param $title
     * @param $key
     * @param $genre
     * @return array
     */
    public function findAll($bpm, $scale, $artist, $title, $key, $genre): array
    {
        return Song::query()->select(self::ATTRIBUTES)
            ->where('bpm', [$bpm - 3, $bpm + 3])
            ->where('scale', $scale)
            ->where('songs.slug', 'like', "%$artist%")
            ->where('title', 'like', "%$title%")
            ->where('key', $key)
            ->orWhereJsonContains('songs.genre', [$genre])
            ->get()->toArray();
    }

    // Find by attribute

    /**
     * @param  string  $attribute
     * @param  string  $value
     * @param  int  $offset
     * @return array
     */
    public function fndByAttribute($attribute, $value, $offset = 0): array
    {
        $songs = Song::query()->select(self::ATTRIBUTES)
            ->where($attribute, 'like', "%$value%")
            ->offset($offset)
          //  ->limit(10)
            ->get();

        return $songs->toArray();
    }

    // find By Multiple Attributes
    /**
     * @param  array  $attributes
     * @param  int  $offset
     * @return array
     */
    public function findByMultipleAttributes(array $attributes, $offset = 0): array
    {
        $where = [];
        foreach ($attributes as $attribute => $value) {
            if ($attribute === 'bpm') {
                $where[] = ['bpm', [$value - 3, $value + 3]];
            } else {
                $where[] = [$attribute, 'like', "%$value%"];
            }
        }

        $songs = Song::query()->select(self::ATTRIBUTES)
            ->where($where)
            ->offset($offset)
            ->limit(10)
            ->get();

        return $songs->toArray();
    }

    /**
     * @param  string  $attribute
     * @param  string|float  $value
     * @param  int  $diff
     * @return Builder[]|Collection
     */
    public function getAttribute(string $attribute, string|float $value, int $diff = 3)
    {
        if (is_float($value)) {
            $songs = Song::query()->select(self::ATTRIBUTES)
                ->whereBetween($attribute, [$value - $diff, $value + $diff])
              //  ->limit(10)
                ->get();
        }

        $songs = Song::query()->select(self::ATTRIBUTES)
            ->where($attribute, 'like', "%$value%")
           // ->limit(10)
            ->get();

        // chunk 10 songs at a time
        return $songs->chunk(10);
    }
}
