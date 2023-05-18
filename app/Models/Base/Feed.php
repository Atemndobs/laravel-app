<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\AdminUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Feed
 * 
 * @property int $id
 * @property string|null $title
 * @property string|null $content
 * @property string|null $payload
 * @property string|null $surce
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $published_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * 
 * @property AdminUser|null $admin_user
 *
 * @package App\Models\Base
 */
class Feed extends Model
{
	protected $table = 'feeds';

	protected $casts = [
		'published_at' => 'date',
		'created_by_id' => 'int',
		'updated_by_id' => 'int'
	];

	public function admin_user()
	{
		return $this->belongsTo(AdminUser::class, 'updated_by_id');
	}
}
