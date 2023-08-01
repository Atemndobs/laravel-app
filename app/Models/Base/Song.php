<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Base;

use App\Models\Usage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Song
 * 
 * @property int $id
 * @property string|null $title
 * @property string|null $author
 * @property string|null $link
 * @property string|null $source
 * @property string|null $key
 * @property string|null $scale
 * @property float|null $bpm
 * @property float|null $duration
 * @property float|null $danceability
 * @property float|null $happy
 * @property float|null $sad
 * @property float|null $relaxed
 * @property float|null $aggressiveness
 * @property float|null $energy
 * @property string|null $comment
 * @property string|null $path
 * @property string|null $extension
 * @property string|null $status
 * @property bool|null $analyzed
 * @property string|null $related_songs
 * @property string|null $genre
 * @property string|null $image
 * @property bool|null $played
 * @property string|null $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $classification_properties
 * @property bool|null $run_analysis
 * @property string|null $song_id
 * 
 * @property Collection|Usage[] $usages
 *
 * @package App\Models\Base
 */
class Song extends Model
{
	protected $table = 'songs';

	protected $casts = [
		'bpm' => 'float',
		'duration' => 'float',
		'danceability' => 'float',
		'happy' => 'float',
		'sad' => 'float',
		'relaxed' => 'float',
		'aggressiveness' => 'float',
		'energy' => 'float',
		'analyzed' => 'bool',
		'played' => 'bool',
		'run_analysis' => 'bool'
	];

	public function usages()
	{
		return $this->belongsToMany(Usage::class, 'usages_songs_links');
	}
}
