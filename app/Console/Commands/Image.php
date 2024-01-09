<?php

namespace App\Console\Commands;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Image extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:fix  {--p|path=} {--a|all}';

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

        $songsCount = Song::query()->count();
        // get all songs where image contains /music/
        $songsWithoutImage = Song::query()->where('image', 'like', '%/music/%')->get();
        // for each song without image, update the image with s3 url
        /** @var Song $song */
        foreach ($songsWithoutImage as $song) {
            $slug = $song->slug;
            // if slug ends with .mp3, remove it
            if (Str::endsWith($slug, 'mp3')) {
                $slug = Str::replaceLast('mp3', '', $slug);
            }
            $song->image = 'https://s3.amazonaws.com/curators3/images/' . $slug . '.jpeg';
            $song->save();
            $message = [
                'slug' => $song->slug,
                'image' => $song->image,
                'path' => $song->path,
            ];
            $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        die('END OF SCRIPT');
        $songsWithImagesCount = Song::query()->whereNotNull('image')->where('image', 'like', '%http%')->get()->count();
        $songsWithoutImagesCount = $songsCount - $songsWithImagesCount;

        $stats = [
            'songsCount' => $songsCount,
            'songsWithImagesCount' => $songsWithImagesCount,
            'songsWithoutImagesCount' => $songsWithoutImagesCount,
        ];
        $this->info(json_encode($stats));

        if ($all !== null) {
            $this->info('Processing all images');
            Song::all()->each(function ($song) use (&$songsWithImage, &$songsWithoutImage) {
                if ($song->image !== null && $song->image !== '') {
                  //  $imageUrl = str_replace('.mp3', '.jpeg', $song->image);
                    $imageUrl = $song->image;
                    try {
                        $this->info('Processing song: ' . $song->title);
                        $req = Http::get($imageUrl);

                        if ($req->successful()) {
                            $song->image = $imageUrl;
                            $song->save();
                            $this->info('Image is valid' . $req->status());
                            $songsWithImage[] = [
                                'slug' => $song->slug,
                                'image' => $imageUrl,
                                'path' => $song->path,
                            ];
                            // writ / add to file songs with image
                            $file = fopen("songsWithImage.txt", 'a');
                            fwrite($file, $song->slug . "\n");
                            fclose($file);
                           // dump($songsWithImage);
                        } else {
                            $this->error($req->status());
                            $this->error('Image is not valid');
                            $song->image = null;
                            $song->save();
                            $songsWithoutImage[] = [
                                'slug' => $song->slug,
                                'image' => $imageUrl,
                                'path' => $song->path,
                            ];
                            // write / add to file songs without image
                            $file = fopen("songsWithoutImage.txt", 'a');
                            fwrite($file, $song->slug . "\n");
                            fclose($file);
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
            'songsWithImage' => count($songsWithImage),
            'songsWithoutImage' => count($songsWithoutImage),
        ]);
        return 0;
    }
    }
