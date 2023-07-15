<?php

namespace App\Models;

use App\Models\Base\SingleRelease as BaseSingleRelease;

class SingleRelease extends BaseSingleRelease
{
	protected $fillable = [
		'status',
		'sort',
		'user_created',
		'date_created',
		'user_updated',
		'date_updated',
		'title',
		'author',
		'album',
		'source',
		'url'
	];
}
