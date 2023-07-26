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
 * @property float|null $bpm_min
 * @property float|null $bpm_max
 * @property string|null $key
 * @property string|null $scale
 * @property string|null $mood
 * @property float|null $happy
 * @property float|null $sad
 * @property string|null $genre
 * @property float|null $energy
 * @property float|null $danceability
 * @property float|null $aggressiveness
 * @property string|null $ip
 * @property string $session_token
 * @property string $status
 * @property int|null $sort
 * @property Carbon $date_created
 * @property Carbon $date_updated
 * @property string|null $user_created
 * @property string|null $user_updated
 * @property float|null $bmp_range
 * 
 * @property DirectusUser|null $directus_user
 *
 * @package App\Models\Base
 */
class MatchCriterion extends Model
{
	protected $table = 'match_criteria';
	public $timestamps = false;

	protected $casts = [
		'bpm' => 'float',
		'bpm_min' => 'float',
		'bpm_max' => 'float',
		'happy' => 'float',
		'sad' => 'float',
		'energy' => 'float',
		'danceability' => 'float',
		'aggressiveness' => 'float',
		'sort' => 'int',
		'date_created' => 'datetime',
		'date_updated' => 'datetime',
		'bmp_range' => 'float'
	];

	public function directus_user()
	{
		return $this->belongsTo(DirectusUser::class, 'user_updated');
	}
}
