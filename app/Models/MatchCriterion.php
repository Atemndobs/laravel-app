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
		'user_created',
		'date_created',
		'user_updated',
		'date_updated',
	];
}
