<?php

namespace App\Console\Commands;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Image extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:check  {--p|path=} {--a|all}';

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
        $all = $this->option('all');
        $path = $this->option('path');
        // if path is not provided, create a folder in root directory called fixed
        if ($path === null) {
            $path = 'fixed';
        }
    
        // log out the full path
        $fullPath = base_path($path);
        $this->info("Full path: $fullPath");
        $songsWithImage = [];
        $songsWithoutImage = [];

        // get all images from storage s3 bucket images folder and extract the slug
        // looks like images/slug.jpg
        $this->info('Getting all images from s3 bucket');
        $s3Images = Storage::disk('s3')->files('images');
        $s3Images = array_map(function ($image) {
            // remove jpg and jpeg from the image name
            $image = str_replace('images/', '', $image);
            $image = str_replace('.jpg', '', $image);
            return str_replace('.jpeg', '', $image);
        }, $s3Images);

        // songs without images are slugs that are in not the s3 bucket (difference between s3 images and songs)
        $slugsWithoutImage = array_diff(Song::query()->pluck('slug')->toArray(), $s3Images);
        $songsCount = Song::query()->count();
        $songsWithImagesCount = count($s3Images);
        $songsWithoutImagesCount = count($slugsWithoutImage);

        // write slugs with images to a file
        $this->warn('Creating file songs_with_image.txt');
        $file = fopen("$path/songs_with_image.txt", 'w');
        foreach ($s3Images as $slug) {
            fputcsv($file, [$slug] );
        }
        fclose($file);

        // write slugs without images to a file
        $this->warn('Creating file songs_without_image.txt');
        $file = fopen("$path/songs_without_image.txt", 'w');
        foreach ($slugsWithoutImage as $slug) {
            fputcsv($file, [$slug] );
        }
        fclose($file);

        if ($path !== null) {
            // get the song_url of songs without images based on slugs and save in a file
            $songsWithoutImage = Song::query()->whereIn('slug', $slugsWithoutImage)->get()->toArray();

            $this->info('Creating file for urls without image');
            $file = fopen("$path/song_urls_without_image.txt", 'w');
            /** @var Song $song */
            foreach ($songsWithoutImage as $song) {
                fputcsv($file, [$song['song_url']] );
            }
            fclose($file);
        }
        if ($path !== null) {
            $this->info('Creating file for urls with image');
            $songsWithImage = Song::query()->whereIn('slug', $s3Images)->get()->toArray();

            $file = fopen("$path/song_urls_with_image.txt", 'w');
            foreach ($songsWithImage as $song) {
                fputcsv($file, [$song['song_url']] );
            }
            fclose($file);
        }

        $stats = [
            'songsCount' => $songsCount,
            'songsWithImagesCount' => $songsWithImagesCount,
            'songsWithoutImagesCount' => $songsWithoutImagesCount,
            'files' => [
                'songsWithImage' => "$path/songs_with_image.txt",
                'songsWithoutImage' => "$path/songs_without_image.txt",
                'songUrlsWithImage' => "$path/song_urls_with_image.txt",
                'songUrlsWithoutImage' => "$path/song_urls_without_image.txt",
            ],
        ];

        $this->info(json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

//        if ($all !== null) {
//
//            $this->info('Processing all images');
////            Song::all()->each(function ($song) use (&$songsWithImage, &$songsWithoutImage) {
////                if ($song->image !== null && $song->image !== '') {
////                  //  $imageUrl = str_replace('.mp3', '.jpeg', $song->image);
////                    $imageUrl = $song->image;
////                    try {
////                        $this->info('Processing song: ' . $song->title);
////                        $req = Http::get($imageUrl);
////
////                        if ($req->successful()) {
////                            $song->image = $imageUrl;
////                            $song->save();
////                            $this->info('Image is valid' . $req->status());
////                            $songsWithImage[] = [
////                                'slug' => $song->slug,
////                                'image' => $imageUrl,
////                                'path' => $song->path,
////                            ];
////                            // writ / add to file songs with image
////                            $file = fopen("songsWithImage.txt", 'a');
////                            fwrite($file, $song->slug . "\n");
////                            fclose($file);
////                           // dump($songsWithImage);
////                        } else {
////                            $this->error($req->status());
////                            $this->error('Image is not valid');
////                            $song->image = null;
////                            $song->save();
////                            $songsWithoutImage[] = [
////                                'slug' => $song->slug,
////                                'image' => $imageUrl,
////                                'path' => $song->path,
////                            ];
////                            // write / add to file songs without image
////                            $file = fopen("songsWithoutImage.txt", 'a');
////                            fwrite($file, $song->slug . "\n");
////                            fclose($file);
////                        }
////                    } catch (\Exception $e) {
////                        $this->error('Error: ' . $e->getMessage());
////                        $songsWithoutImage[] = [
////                            'slug' => $song->slug,
////                            'image' => $imageUrl,
////                            'path' => $song->path,
////                        ];
////                    }
////
////                } else {
////                    $songsWithoutImage[] = $song;
////                }
////            });
//        }

        return 0;
    }
    }
