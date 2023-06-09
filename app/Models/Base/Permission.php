<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\ModelHasPermission;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Permission
 * 
 * @property int $id
 * @property string|null $name
 * @property string|null $guard_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $key
 * @property string|null $table_name
 * 
 * @property Collection|ModelHasPermission[] $model_has_permissions
 * @property Collection|Role[] $roles
 *
 * @package App\Models\Base
 */
class Permission extends Model
{
	protected $table = 'permissions';

	public function model_has_permissions()
	{
		return $this->hasMany(ModelHasPermission::class);
	}

	public function roles()
	{
		return $this->belongsToMany(Role::class, 'role_has_permissi