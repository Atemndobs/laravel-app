<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SpotifyAuth
 * 
 * @property int $id
 * @property Carbon|null $date_created
 * @property Carbon|null $date_updated
 * @property string|null $access_token
 * @property string|null $expires
 * @property string|null $refresh_token
 * @property string|null $auth_url
 *
 * @package App\Models\Base
 */
class SpotifyAuth extends Model
{
	protected $table = 'spotify_auth';
	public $timestamps = false;

	protected $casts = [
		'date_created' => 'datetime',
		'date_updated' => 'datetime'
	];
}
