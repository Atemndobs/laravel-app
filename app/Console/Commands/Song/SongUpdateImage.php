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

            $message = [
                'songs_without_image' => count($songs),
            ];
            $this->line("<fg=yellow>" . json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</>");

            if ($slug !== null) {
                $this->info("updating image for |  ".$slug);
                $song = Song::query()->where('slug' ,'=', $slug)->get()->first();
                $updatedSongs['image'] = $service->getSongImage($song)->image;
                $song->image = "https://curators3.s3.amazonaws.com/images/$song->slug.jpeg";
                $song->save();
                $this->progressOutputInfo($song);
                $this->table([ 'image'], [$updatedSongs]);
                return 0;
            }

            // read all songs without image from file path $path
            if ($path !== null) {
                $this->line("updating image for |  ".$path);
                $songsWithoutImage = file_get_contents($path);
                $songsWithoutImage = explode("\n", $songsWithoutImage);

                // Start progress bar
                $bar = $this->output->createProgressBar(count($songsWithoutImage));
                foreach ($songsWithoutImage as $slug) {
                    $bar->advance();
                    $this->line("updating image for |  ".$slug);
                    /**
                     * @var Song $song
                     */
                    $song = Song::query()->where('slug' ,'=', $slug)->get()->first();
                    $updatedSongs['image'] = $service->getSongImage($song)->image;
                    $song->image = "https://curators3.s3.amazonaws.com/images/$song->slug.jpeg";
                    $song->save();
                    $this->progressOutputInfo($song);
                }
                $this->table([ 'image'], [$updatedSongs]);
                return 0;
            }


            // start progress bar
            $bar = $this->output->createProgressBar(count($songs));
            /**
             * @var Song $song
             */
            foreach ($songs as $song) {
                $bar->advance(); 
                $this->line("updating image for |  ".$song->slug);
                $updatedSongs['image'] = $service->getSongImage($song)->image;
                $song->image = "https://curators3.s3.amazonaws.com/images/$song->slug.jpeg";
                $song->save();
                $this->progressOutputInfo($song);
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

                    $bar = $this->output->createProgressBar(count($found));
                    foreach ($found as $song) {
                        $bar->advance();
                        if (strlen($song['image']) > 0) {
                            $this->info("Song already has image" . $song['image']);
                        } else {
                            $this->info("Updating image for song ");
                            $this->call("song:duration", ['slug' => $slug]);
                            $song_slug = $song['slug'];
                            $song['image'] = "https://curators3.s3.amazonaws.com/images/$song_slug.jpeg";
                            $this->progressOutputInfo($song);
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
            $this->progressOutputInfo($song);
        }

        return 0;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|Song $song
     * @return void
     */
    public function progressOutputInfo(\Illuminate\Database\Eloquent\Builder|Song $song): void
    {
        $message = [
            'image3' => $song->image,
            'slug' => $song->slug,
            'song_slug' => $song->slug,
        ];
        $this->line("<fg=magenta>" . json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</>");
    }
}
