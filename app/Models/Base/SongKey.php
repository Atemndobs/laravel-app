<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SongKey
 * 
 * @property int $id
 * @property string $key_name
 * @property int $key_number
 * @property string $scale
 *
 * @package App\Models\Base
 */
class SongKey extends Model
{
	protected $table = 'song_keys';
	public $timestamps = false;

	protected $casts = [
		'key_number' => 'int'
	];
}
