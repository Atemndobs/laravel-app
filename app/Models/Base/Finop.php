<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Finop
 * 
 * @property int $id
 * @property string $status
 * @property int|null $sort
 * @property string|null $question_id
 * @property string|null $question
 * @property string|null $explanation
 * @property string|null $answer
 *
 * @package App\Models\Base
 */
class Finop extends Model
{
	protected $table = 'finops';
	public $timestamps = false;

	protected $casts = [
		'sort' => 'int'
	];
}
