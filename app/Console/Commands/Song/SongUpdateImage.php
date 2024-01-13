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
    protected $signature = 'song:image {slug?}  {--p|path=} ';

    /**
     * The console command description
     *
     * @var string
     */
    protected $description = 'Update song image from spotify where oath is the path to the file containing the slugs of the songs to update (songs without image)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $slug = $this->argument('slug');
        $path = $this->option('path');


        if (strlen($slug) === 0) {
            // info updating all songs without image
            $songs = Song::query()
                ->where('image', '=', '')
                ->orWhere('image', '=', null)
                ->get();

            $service = new SongUpdateService();
            $updatedSongs = [];

            if ($slug !== null) {
                $this->info("updating image for |  ".$slug);
                $song = Song::query()->where('slug' ,'=', $slug)->get()->first();
                $updatedSongs['image'] = $service->getSongImage($song)->image;
                $song->image = "https://curators3.s3.amazonaws.com/images/$song->slug.jpeg";
                $song->save();
                $this->table([ 'image'], [$updatedSongs]);
                return 0;
            }

            // read all songs without image from file path $path
            if ($path !== null) {
                $this->info("updating image for |  ".$path);
                $songsWithoutImage = file_get_contents($path);
                $songsWithoutImage = explode("\n", $songsWithoutImage);

                foreach ($songsWithoutImage as $slug) {
                    $this->info("updating image for |  ".$slug);
                    /**
                     * @var Song $song
                     */
                    $song = Song::query()->where('slug' ,'=', $slug)->get()->first();
                    $updatedSongs['image'] = $service->getSongImage($song)->image;
                    $song->image = "https://curators3.s3.amazonaws.com/images/$song->slug.jpeg";
                    $song->save();
                    dump([
                        'image4' => $song->image,
                    ]);
                }
                $this->table([ 'image'], [$updatedSongs]);
                return 0;
            }

            /**
             * @var Song $song
             */
            foreach ($songs as $song) {
                $this->info("updating image for |  ".$song->slug);
                $updatedSongs['image'] = $service->getSongImage($song)->image;
                $song->image = "https://curators3.s3.amazonaws.com/images/$song->slug.jpeg";
                $song->save();
                dump([
                    'image3' => $song->image,
                    'slug' => $slug,
                    'song_slug' => $song->slug,
                ]);
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
                            $song_slug = $song['slug'];
                            $song['image'] = "https://curators3.s3.amazonaws.com/images/$song_slug.jpeg";
                            dump([
                                'image1' => $song['image'],
                            ]);
                            $song->save();
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
            /**
             * @var Song $song
             */
            $song = Song::query()->where('slug', '=', "$slug")->get()->first();
            $song->image = "https://curators3.s3.amazonaws.com/images/$song->slug.jpeg";
            $song->save();
            dump([
                'image5' => $song->image,
            ]);
        }
        return 0;
    }
}
