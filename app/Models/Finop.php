<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use App\Models\Base\Finop as BaseFinop;
use Backpack\CRUD\app\Library\CrudPanel\Traits\Search;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Maize\Markable\Markable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Finop
 *
 * @property int $id
 * @property string|null $status
 * @property int|null $sort
 * @property int|null $question_id
 * @property string|null $question
 * @property string|null $explanation
 * @property string|null $answer
 *
 */
class Finop extends BaseFinop
{
    use CrudTrait, HasRoles, Search, Searchable, HasApiTokens, HasFactory, Notifiable, Markable, Filterable;

    protected $fillable = [
		'status',
		'sort',
		'question_id',
		'question',
		'explanation',
		'answer'
	];
}
