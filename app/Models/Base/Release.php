<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Release
 * 
 * @property string $id
 * @property string $status
 * @property int|null $sort
 * @property string|null $source
 * @property string|null $type
 * @property string|null $name
 * @property string|null $owner
 * @property int|null $tracks
 * @property string|null $url
 *
 * @package App\Models\Base
 */
class Release extends Model
{
	protected $table = 'releases';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'sort' => 'int',
		'tracks' => 'int'
	];
}
