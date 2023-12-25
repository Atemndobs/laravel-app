<?php

namespace App\Http\Controllers\Api;

use App\Models\Song;
use Orion\Concerns\DisableAuthorization;
use Orion\Concerns\DisablePagination;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;
use Illuminate\Database\Eloquent\Builder;

class SongController extends Controller
{
    use DisableAuthorization;
   // use DisablePagination;

    /**
     * @var string
     */
    protected $model = Song::class;

    /**
     * The list of attributes to select from db
     */
    protected $attributes = [
        'id',
        'title',
        'key',
        'scale',
        'bpm',
        'energy',
        'happy',
        'sad',
        'relaxed',
        'aggressiveness',
        'danceability',
        'path',
        'related_songs',
        'extension',
        'status',
        'author',
        'comment',
        'link',
        'image',
        'source',
        'genre',
        'slug',
        'duration',
    ];

    public function filterableBy(): array
    {
        return [
            'title',
            'key',
            'scale',
            'bpm',
            'energy',
            'happy',
            'sad',
            'relaxed',
            'aggressiveness',
            'danceability',
            'path',
            'related_songs',
            'extension',
            'status',
            'author',
            'comment',
            'link',
            'image',
            'source',
            'genre',
            'slug',
            'duration',
        ];
    }

    public function searchableBy(): array
    {
        return [
            'title',
            'key',
            'scale',
            'bpm',
            'energy',
            'happy',
            'sad',
            'relaxed',
            'aggressiveness',
            'danceability',
            'path',
            'related_songs',
            'extension',
            'status',
            'author',
            'comment',
            'link',
            'image',
            'source',
            'genre',
            'slug',
            'duration',
        ];
    }

    /**
     * Builds Eloquent query for fetching entity(-ies).
     *
     * @param Request $request
     * @param array $requestedRelations
     * @return Builder
     */
    protected function buildFetchQuery(Request $request, array $requestedRelations): Builder
    {
        $query = parent::buildFetchQuery($request, $requestedRelations);
        return $query;
    }

}
