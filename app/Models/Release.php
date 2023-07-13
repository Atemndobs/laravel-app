<?php

namespace App\Models;

use App\Models\Base\Release as BaseRelease;

class Release extends BaseRelease
{
	protected $fillable = [
		'status',
		'sort',
		'source',
		'type',
		'name',
		'owner',
		'tracks',
		'url'
	];
}
