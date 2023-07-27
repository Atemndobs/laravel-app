<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SongKey
 * 
 * @property int $id
 * @property string $key_name
 * @property int $key_number
 * @property string $scale
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Base
 */
class SongKey extends Model
{
	protected $table = 'song_keys';

	protected $casts = [
		'key_number' => 'int'
	];
}
