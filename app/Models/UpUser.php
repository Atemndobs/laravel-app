<?php

namespace App\Models;

use App\Models\Base\UpUser as BaseUpUser;
use Backpack\CRUD\app\Library\CrudPanel\Traits\Search;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Spatie\Permission\Traits\HasRoles;

class UpUser extends BaseUpUser
{
    use CrudTrait, HasRoles, Search, Searchable, HasApiTokens, HasFactory, Notifiable;

    protected $table = 'up_users';

    protected $hidden = [
        'password',
        'reset_password_token',
        'confirmation_token',
    ];

    protected $fillable = [
        'username',
        'email',
        'provider',
        'password',
        'reset_password_token',
        'confirmation_token',
        'confirmed',
        'blocked',
        'created_by_id',
        'updated_by_id',
    ];
}
