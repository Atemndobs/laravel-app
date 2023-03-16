<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\AdminUser;
use App\Models\Song;
use App\Models\UsagesUsersPermissionsUsersLink;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Usage
 *
 * @property int $id
 * @property bool|null $has_played
 * @property int|null $play_count
 * @property bool|null $like
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $published_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property AdminUser|null $admin_user
 * @property Collection|Song[] $songs
 * @property UsagesUsersPermissionsUsersLink $usages_users_permissions_users_link
 */
class Usage extends Model
{
    protected $table = 'usages';

    protected $casts = [
        'has_played' => 'bool',
        'play_count' => 'int',
        'like' => 'bool',
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

    public function songs()
    {
        return $this->belongsToMany(Song::class, 'usages_songs_links');
    }

    public function usages_users_permissions_users_link()
    {
        return $this->hasOne(UsagesUsersPermissionsUsersLink::class);
    }
}
