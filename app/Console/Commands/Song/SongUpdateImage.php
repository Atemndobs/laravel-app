<?php

namespace App\Console\Commands\Song;

use App\Models\Setting;
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
        $totalSongs = Song::query()->count();
        $songsWithoutImageCount = 0;

        $s3_base_url = Setting::query()->where('key', 'base_url')
            ->where('group', 's3')
            ->first()->value;
        $bucket = Setting::query()->where('key', 'bucket')
            ->where('group', 's3')
            ->first()->value;


        if (strlen($slug) === 0) {
            // info updating all songs without image
            $songs = Song::query()
                ->where('image', '=', '')
                ->orWhere('image', '=', null)
                ->get();
            $songsWithoutImageCount = count($songs);

            $service = new SongUpdateService();
            $updatedSongs = [];

            $message = [
                'songs_without_image' => $songsWithoutImageCount,
                'total_songs' => $totalSongs,
            ];
            $this->line("<fg=yellow>" . json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</>");

            if ($slug !== null) {
                $song = Song::query()->where('slug' ,'=', $slug)->get()->first();
                $updatedSongs = $this->updateImage($song, $service, $updatedSongs);
                $this->progressOutputInfo($song, $songsWithoutImageCount);
                $this->table([ 'image'], [$updatedSongs]);
                return 0;
            }

            // read all songs without image from file path $path
            if ($path !== null) {
                $this->line("updating image for |  ".$path);
                $songsWithoutImage = file_get_contents($path);
                $songsWithoutImage = explode("\n", $songsWithoutImage);
                $songsWithoutImageCount = count($songsWithoutImage);

                // Start progress bar
                $bar = $this->output->createProgressBar($songsWithoutImageCount);
                foreach ($songsWithoutImage as $slug) {
                    $bar->advance();
                    /**
                     * @var Song $song
                     */
                    $song = Song::query()->where('slug' ,'=', $slug)->get()->first();
                    $updatedSongs = $this->updateImage($song, $service, $updatedSongs);
                    $this->progressOutputInfo($song, $songsWithoutImageCount);
                }
                $this->table([ 'image'], [$updatedSongs]);
                return 0;
            }


            // start progress bar
            $bar = $this->output->createProgressBar($songsWithoutImageCount);
            /**
             * @var Song $song
             */
            foreach ($songs as $song) {
                $bar->advance();
                // if the song slug is one of the slugs in the file withoutImage.txt, skip it
                // open the file and read the slugs
                $songsToSkip = file_get_contents('withoutImage.txt');
                $songsToSkip = explode("\n", $songsToSkip);
                if (in_array($song->slug, $songsToSkip)) {
                    $this->info("Skipping song with slug " . $song->slug);
                    continue;
                }

                $updatedSongs = $this->updateImage($song, $service, $updatedSongs);
                $this->progressOutputInfo($song, $songsWithoutImageCount);
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

                    $bar = $this->output->createProgressBar($songsWithoutImageCount);
                    foreach ($found as $song) {
                        $bar->advance();
                        if (strlen($song['image']) > 0) {
                            $this->info("Song already has image" . $song['image']);
                        } else {
                            $this->info("Updating image for song ");
                            $this->call("song:duration", ['slug' => $slug]);
                            $song_slug = $song['slug'];
                            $song_image = $s3_base_url . "/$bucket/". '/images/' . "$song_slug.jpeg";
                            $song['image'] = $song_image;
                            $this->progressOutputInfo($song, $songsWithoutImageCount);
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
            $song_image = $s3_base_url . "/$bucket/". '/images/' . "$song->slug.jpeg";
            $song->image = $song_image;
            $song->save();
            $this->progressOutputInfo($song, $songsWithoutImageCount);
        }

        return 0;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|Song $song
     * @return void
     */
    public function progressOutputInfo(\Illuminate\Database\Eloquent\Builder|Song $song, $songsWithoutImage): void
    {
        $remaining = Song::query()
            ->where('image', '=', '')
            ->orWhere('image', '=', null)
            ->count();
        $message = [
            'image3' => $song->image,
            'slug' => $song->slug,
            'song_slug' => $song->slug,
            'Songs_without_image' => $songsWithoutImage,
            'remaining' => $remaining,
            'processed' => $songsWithoutImage - $remaining,
        ];
        $this->line("<fg=magenta>" . json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</>");
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|Song $song
     * @param SongUpdateService $service
     * @param array $updatedSongs
     * @return array
     */
    public function updateImage(\Illuminate\Database\Eloquent\Builder|Song $song, SongUpdateService $service, array $updatedSongs): array
    {
        $this->line("");
        $this->line("<fg=green>" . "updating image for |  " . $song->slug . "</>");
        $s3_base_url = Setting::query()->where('key', 'base_url')
            ->where('group', 's3')
            ->first()->value;
        $bucket = Setting::query()->where('key', 'bucket')
            ->where('group', 's3')
            ->first()->value;

        try {
            $updatedSongs['image'] = $service->getSongImage($song)->image;
            $song_image = $s3_base_url . "/$bucket/". '/images/' . "$song->slug.jpeg";
            $song->image = $song_image;
            $song->save();
        } catch (\Exception $e) {
            $this->line("<fg=red>" . $e->getMessage() . "</>");
        }
        return $updatedSongs;
    }
}
