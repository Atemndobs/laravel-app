<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Backpack\CRUD\app\Library\CrudPanel\Traits\Search;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Maize\Markable\Markable;
use Maize\Markable\Models\Bookmark;
use Maize\Markable\Models\Favorite;
use Maize\Markable\Models\Like;
use Maize\Markable\Models\Reaction;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Song
 *
 * @property int $id
 * @property string|null $title
 * @property bool|null $analyzed
 * @property string|null $status
 * @property string|null $key
 * @property string|null $scale
 * @property float|null $bpm
 * @property float|null $energy
 * @property float|null $happy
 * @property float|null $sad
 * @property float|null $aggressiveness
 * @property float|null $danceability
 * @property float|null $relaxed
 * @property string|null $path
 * @property string|null $related_songs
 * @property string|null $extension
 * @property string|null $author
 * @property string|null $comment
 * @property string|null $source
 * @property string|null $link
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property string|null $slug
 * @property string|null $duration
 * @property AdminUser|null $admin_user
 *
 */
class Song extends \App\Models\Base\Song
{
    use CrudTrait, HasRoles, Search, Searchable, HasApiTokens, HasFactory, Notifiable, Markable;

    protected $table = 'songs';


    protected static $marks = [
        Like::class,
        Favorite::class,
        Bookmark::class,
        Reaction::class,
    ];

    protected $casts = [
        'bpm' => 'float',
        'danceability' => 'float',
        'happy' => 'float',
        'sad' => 'float',
        'relaxed' => 'float',
        'aggressiveness' => 'float',
        'energy' => 'float',
        'created_by_id' => 'int',
        'updated_by_id' => 'int',
        'extension' => 'string',
        'genre' => 'array',
    ];

    protected $dates = [
        'published_at',
    ];

    protected $fillable = [
        'title',
        'analyzed',
        'key',
        'scale',
        'bpm',
        'energy',
        'happy',
        'sad',
        'relaxed',
        'aggressiveness',
        'danceability',
        'path',
        'related_songs',
        'extension',
        'status',
        'author',
        'comment',
        'link',
        'image',
        'source',
        'genre',
    ];

    public function admin_user()
    {
        return $this->belongsTo(AdminUser::class, 'updated_by_id');
    }

    /*    public function uploadFileToDisk($value, $attribute_name, $disk, $destination_path)
        {

            // if a new file is uploaded, delete the file from the disk
            if (request()->hasFile($attribute_name) &&
                $this->{$attribute_name} &&
                $this->{$attribute_name} != null) {
                Storage::disk($disk)->delete($this->{$attribute_name});
                $this->attributes[$attribute_name] = null;
            }


            // if the file input is empty, delete the file from the disk
            if (is_null($value) && $this->{$attribute_name} != null) {
                Storage::disk($disk)->delete($this->{$attribute_name});
                $this->attributes[$attribute_name] = null;
            }

            //      dd($attribute_name);
            // if a new file is uploaded, store it on disk and its filename in the database
            if (request()->hasFile($attribute_name) && request()->file($attribute_name)->isValid()) {

                // 1. Generate a new file name
                $file = request()->file($attribute_name);
               // $new_file_name = md5($file->getClientOriginalName().random_int(1, 9999).time()).'.'.$file->getClientOriginalExtension();
                $new_file_name = $file->getClientOriginalName();
                str_replace('&', '-', $new_file_name);

                // 2. Move the new file to the correct path
                $file_path = $file->storeAs($destination_path, $new_file_name, $disk);

               // $full_path = asset('storage/'.$file_path);

                $full_path = asset(Storage::url($file_path));
                // 3. Save the complete path to the database
                $this->attributes[$attribute_name] = $full_path;
            }
        }

        public function uploadMultipleFilesToDisk($value, $attribute_name, $disk, $destination_path)
        {
            if (! is_array($this->{$attribute_name})) {
                $attribute_value = json_decode($this->{$attribute_name}, true) ?? [];
            } else {
                $attribute_value = $this->{$attribute_name};
            }
            $files_to_clear = request()->get('clear_'.$attribute_name);

            // if a file has been marked for removal,
            // delete it from the disk and from the d
            if ($files_to_clear) {
                foreach ($files_to_clear as $key => $filename) {
                    \Storage::disk($disk)->delete($filename);
                    $attribute_value = Arr::where($attribute_value, function ($value, $key) use ($filename) {
                        return $value != $filename;
                    });
                }
            }
            // if a new file is uploaded, store it on disk and its filename in the database
            if (request()->hasFile($attribute_name)) {
                foreach (request()->file($attribute_name) as $file) {
                    if ($file->isValid()) {
                        // $new_file_name = md5($file->getClientOriginalName().random_int(1, 9999).time()).'.'.$file->getClientOriginalExtension();
                        $new_file_name = $file->getClientOriginalName();
                        str_replace('&', '-', $new_file_name);

                        // 2. Move the new file to the correct path
                        $file_path = $file->storeAs($destination_path, $new_file_name, $disk);

                        // $full_path = asset('storage/'.$file_path);

                        $full_path = asset(Storage::url($file_path));

                        // 3. Add the public path to the database
                        $attribute_value[] = $file_path;
                    }
                }
            }
            $this->attributes[$attribute_name] = json_encode($attribute_value);
        }*/
}
