<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SingleRelease
 * 
 * @property string $id
 * @property string $status
 * @property int|null $sort
 * @property string|null $user_created
 * @property Carbon|null $date_created
 * @property string|null $user_updated
 * @property Carbon|null $date_updated
 * @property string|null $title
 * @property string|null $author
 * @property string|null $source
 * @property string|null $album
 * @property string|null $url
 * @property string|null $image
 * @property Carbon|null $added_at
 *
 * @package App\Models\Base
 */
class SingleRelease extends Model
{
	protected $table = 'single_releases';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'sort' => 'int',
		'date_created' => 'datetime',
		'date_updated' => 'datetime',
		'added_at' => 'datetime'
	];
}
