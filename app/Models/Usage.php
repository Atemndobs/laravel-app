<?php

namespace App\Models;

use App\Models\Base\Usage as BaseUsage;
use Backpack\CRUD\app\Library\CrudPanel\Traits\Search;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Spatie\Permission\Traits\HasRoles;

class Usage extends BaseUsage
{
    use CrudTrait, HasRoles, Search, Searchable, HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usages';

    protected $fillable = [
        'has_played',
        'play_count',
        'like',
        'published_at',
        'created_by_id',
        'updated_by_id',
    ];
}
