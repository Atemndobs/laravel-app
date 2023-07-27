<?php

namespace App\Models;

use App\Models\Base\SongKey as BaseSongKey;

class SongKey extends BaseSongKey
{
	protected $fillable = [
		'key_name',
		'key_number',
		'scale'
	];
}
