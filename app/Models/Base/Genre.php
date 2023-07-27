<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Genre
 * 
 * @property int $id
 * @property string $name
 *
 * @package App\Models\Base
 */
class Genre extends Model
{
	protected $table = 'genres';
}
