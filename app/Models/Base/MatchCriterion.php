<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\DirectusUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MatchCriterion
 * 
 * @property int $id
 * @property float|null $bpm
 * @property string|null $key
 * @property float|null $bpm_min
 * @property float|null $bpm_max
 * @property string|null $scale
 * @property float|null $happy
 * @property float|null $sad
 * @property string|null $mood
 * @property float|null $energy
 * @property float|null $danceability
 * @property float|null $aggressiveness
 * @property string|null $ip
 * @property string|null $session_token
 * @property string $status
 * @property int|null $sort
 * @property string|null $user_created
 * @property Carbon|null $date_created
 * @property string|null $user_updated
 * @property Carbon|null $date_updated
 77* @property DirectusUser|null $directus_user
 *
 * @package App\Models\Base
 */
class MatchCriterion extends Model
{
	protected $table = 'match_criteria';
	public $timestamps = false;

	protected $casts = [
		'sort' => 'int',
		'date_created' => 'date',
		'date_updated' => 'date',
		'bpm' => 'float',
		'bpm_min' => 'float',
		'bpm_max' => 'float',
		'happy' => 'float',
		'sad' => 'float',
		'energy' => 'float',
		'danceability' => 'float',
		'aggressiveness' => 'float',
        'ip' => 'string',
        'session_token' => 'string',
	];

//	public function directus_user()
//	{
//		return $this->belongsTo(DirectusUser::class, 'user_updated');
//	}
}
