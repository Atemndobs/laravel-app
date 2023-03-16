<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Feed
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $payload
 * @property string $source
 * @property string $image
 * @property string $since
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Feed extends \App\Models\Base\Feed
{
    use CrudTrait;
    use Notifiable;
    use HasRoles;

    protected $table = 'feeds';

    protected $fillable = [
        'title',
        'content',
        'payload',
        'source',
    ];
}
