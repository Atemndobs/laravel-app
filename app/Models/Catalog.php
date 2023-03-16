<?php

namespace App\Models;

use App\Models\Base\Catalog as BaseCatalog;
use Backpack\CRUD\app\Library\CrudPanel\Traits\Search;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Spatie\Permission\Traits\HasRoles;

class Catalog extends BaseCatalog
{
    use CrudTrait, HasRoles, Search, Searchable, HasApiTokens, HasFactory, Notifiable;

    protected $table = 'catalogs';

    protected $fillable = [
        'item_id',
        'item_name',
        'item_category',
        'description',
        'features_list',
        'published_at',
        'created_by_id',
        'updated_by_id',
    ];
}
