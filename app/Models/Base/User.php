<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\MarkableFavorite;
use App\Models\MarkableLike;
use App\Models\MarkableReaction;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * 
 * @property int $id
 * @property int|null $role_id
 * @property string $name
 * @property string $email
 * @property bool|null $super
 * @property string|null $preferences
 * @property Carbon|null $last_login
 * @property string $password
 * @property string|null $avatar
 * @property Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property string|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $spotify_id
 * @property string|null $session
 * 
 * @property Role|null $role
 * @property Collection|Role[] $roles
 *
 * @package App\Models\Base
 */
class User extends Model
{

	protected $table = 'users';

	protected $casts = [
		'role_id' => 'int',
		'super' => 'bool',
		'last_login' => 'datetime',
		'email_verified_at' => 'datetime'
	];

	public function role()
	{
		return $this->belongsTo(Role::class);
	}

	public function roles()
	{
		return $this->belongsToMany(Role::class, 'user_roles');
	}
}
