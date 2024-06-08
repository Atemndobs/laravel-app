<?php

namespace App\Models;

use App\Models\Base\Setting as BaseSetting;

class Setting extends BaseSetting
{
	protected $fillable = [
		'key',
		'display_name',
		'value',
		'details',
		'type',
		'order',
		'group'
	];
}
