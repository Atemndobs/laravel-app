<?php

namespace App\Models;

use App\Models\Base\SpotifyAuth as BaseSpotifyAuth;

class SpotifyAuth extends BaseSpotifyAuth
{
	protected $hidden = [
		'access_token'
	];

	protected $fillable = [
		'date_created',
		'date_updated',
		'access_token',
		'refresh_tiken',
		'expires'
	];
}
