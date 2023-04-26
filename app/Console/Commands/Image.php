<?php

namespace App\Console\Commands;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Http;

class Image extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:fix  {--p|path=} {all?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public const BASE_URL = 'http://host.docker.internal:3000';
    public const BASE_DOCKER = 'http://nginx';

    /**s
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $all = $this->argument('all');
        $path = $this->option('path');
        $songsWithImage = [];
        $songsWithoutImage = [];

        $songsCount = Song::query()->count();
        $songsWithImagesCount = Song::query()->whereNotNull('image')->where('image', 'like', '%http%')->get()->count();
        $songsWithoutImagesCount = $songsCount - $songsWithImagesCount;

        $stats = [
            'songsCount' => $songsCount,
            'songsWithImagesCount' => $songsWithImagesCount,
            'songsWithoutImagesCount' => $songsWithoutImagesCount,
        ];
        info(json_encode($stats));

        $base_url = env('APP_ENV') == 'local' ? self::BASE_DOCKER : env('APP_URL');

        if ($all !== null) {
            $this->info('Processing all images');
            Song::all()->each(function ($song) use (&$songsWithImage, &$songsWithoutImage, $base_url) {

                if ($song->image !== null && $song->image !== '') {
//                    $imageUrl = str_replace('127.0.0.1:3000/music', "$base_url/storage/images", $song->image);
//                    $song->image = $imageUrl;
//                    $song->save();
//                    $imageUrl = str_replace('mage.tech:8899', $base_url, $song->image);
                    // get image from song and  change extension from .mp3 to .jpg
                    $imageUrl = str_replace('.mp3', '.jpeg', $song->image);
                    $song->image = $imageUrl;
                    $song->save();
                    try {
                        $this->info('Processing song: ' . $song->title);
                        $req = Http::get($imageUrl);

                        if ($req->successful()) {
                            $this->info('Image is valid' . $req->status());
                            $songsWithImage[] = [
                                'slug' => $song->slug,
                                'image' => $imageUrl,
                                'path' => $song->path,
                            ];
                            dump($songsWithImage);
                        } else {
                            $this->error($req->status());
                            $this->error('Image is not valid');
                            $songsWithoutImage[] = [
                                'slug' => $song->slug,
                                'image' => $imageUrl,
                                'path' => $song->path,
                            ];
                        }
                    } catch (\Exception $e) {
                        $this->error('Error: ' . $e->getMessage());
                        $songsWithoutImage[] = [
                            'slug' => $song->slug,
                            'image' => $imageUrl,
                            'path' => $song->path,
                        ];
                    }

                } else {
                    $songsWithoutImage[] = $song;
                }
            });
        }

        // create a file and save all songs without image
        if ($path !== null) {
            $this->info('Creating file');
            $file = fopen("$path/songs_without_image.txt", 'w');
            foreach ($songsWithoutImage as $song) {
                fputcsv($file, [$song['slug']] );
            }
            fclose($file);
        }

        if ($path !== null) {
            $this->info('Creating file');
            $file = fopen("$path/songs_with_image.txt", 'w');
            foreach ($songsWithImage as $song) {
                fputcsv($file, [$song['slug']] );
            }
            fclose($file);
        }



        dump([
            'songsWithImage' => $songsWithImage,
            'songsWithoutImage' => $songsWithoutImage
        ]);
        return 0;
    }
    }
