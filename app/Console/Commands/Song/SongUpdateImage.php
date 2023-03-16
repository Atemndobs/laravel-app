<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use App\Services\SongUpdateService;
use Illuminate\Console\Command;
use function example\int;

class SongUpdateImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:image {slug?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $slug = $this->argument('slug');
        if (strlen($slug) === 0) {
            // info updating all songs without image
            $songs = Song::query()
                ->where('image', '=', '')
                ->orWhere('image', '=', null)
                ->get();

            $service = new SongUpdateService();
            $updatedSongs = [];
            foreach ($songs as $song) {

                $this->info("updating image for |  ".$song->slug);
                $updatedSongs['image'] = $service->getSongImage($song)->image;
            }
            $this->table([ 'image'], [$updatedSongs]);
            return 0;
            } else {
            try {
                $existing = Song::query()->where('slug', '=', "$slug")
                    ->get('image')->toArray();
                if (count($existing) == 0) {
                    $this->info("No song found with slug $slug");
                    $found= Song::query()->where('slug', 'like', "%$slug%")
                        ->get('image')->toArray();
                    $this->info("Found ".count($found)." songs with slug like  $slug");
                    foreach ($found as $song) {
                        if (strlen($song['image']) > 0) {
                            $this->info("Song already has image" . $song['image']);
                        } else {
                            $this->info("Updating image for song ");
                            $this->call("song:duration", ['slug' => $slug]);
                        }
                    }
                    return 0;
                }
                if ((int)$existing[0]['image'] != 0) {
                    $this->info('Image already exists');
                    $this->table(['image'], $existing);
                    return 0;
                }
            }catch (\Exception $e) {
                $this->error($e->getMessage());
                return 1;
            }
            $this->call("song:duration", ['slug' => $slug]);
        }
        return 0;
    }
}
