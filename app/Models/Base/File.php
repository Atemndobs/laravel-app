<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\AdminUser;
use App\Models\FilesFolderLink;
use App\Models\FilesRelatedMorph;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class File
 * 
 * @property int $id
 * @property string|null $name
 * @property string|null $alternative_text
 * @property string|null $caption
 * @property int|null $width
 * @property int|null $height
 * @property string|null $formats
 * @property string|null $hash
 * @property string|null $ext
 * @property string|null $mime
 * @property float|null $size
 * @property string|null $url
 * @property string|null $preview_url
 * @property string|null $provider
 * @property string|null $provider_metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * 
 * @property AdminUser|null $admin_user
 * @property FilesFolderLink $files_folder_link
 * @property FilesRelatedMorph $files_related_morph
 *
 * @package App\Models\Base
 */
class File extends Model
{
    protected $table = 'files';

    protected $casts = [
        'width' => 'int',
        'height' => 'int',
        'size' => 'float',
        'created_by_id' => 'int',
        'updated_by_id' => 'int'
    ];

    public function admin_user()
    {
        return $this->belongsTo(AdminUser::class, 'updated_by_id');
    }

    public function files_folder_link()
    {
        return $this->hasOne(FilesFolderLink::class);
    }

}