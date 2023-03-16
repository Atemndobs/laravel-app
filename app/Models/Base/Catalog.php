<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\AdminUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Catalog
 *
 * @property int $id
 * @property string|null $item_id
 * @property string|null $item_name
 * @property string|null $item_category
 * @property string|null $description
 * @property string|null $features_list
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $published_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property AdminUser|null $admin_user
 */
class Catalog extends Model
{
    protected $table = 'catalogs';

    protected $casts = [
        'created_by_id' => 'int',
        'updated_by_id' => 'int',
    ];

    protected $dates = [
        'published_at',
    ];

    public function admin_user()
    {
        return $this->belongsTo(AdminUser::class, 'updated_by_id');
    }
}
