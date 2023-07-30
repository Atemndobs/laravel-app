<?php

namespace App\Models;

use App\Models\Base\MatchCriterion as BaseMatchCriterion;

class MatchCriterion extends BaseMatchCriterion
{
    protected $fillable = [
		'bpm',
		'key',
		'bpm_min',
		'bpm_max',
		'scale',
		'happy',
		'sad',
		'mood',
		'energy',
		'danceability',
		'aggressiveness',
        'ip',
        'session_token',
        'status',
		'sort',
        'played_songs',
        'bpm_range',
		'user_created',
		'date_created',
		'user_updated',
		'date_updated',
	];

    public function addPlayedSongs(int $id): void
    {
        $this->played_songs = $this->played_songs . ',' . $id;
        // remove duplicates
        $this->played_songs = implode(',', array_unique(explode(',', $this->played_songs)));
        // remove trailing comma at the beginning
        $this->played_songs = ltrim($this->played_songs, ',');
        $this->update();
    }

    public function getPlayedSongs(): array
    {
        return explode(',', $this->played_songs);
    }

    public function getPlayedSongsCount()
    {
        return count($this->getPlayedSongs());
    }

}
