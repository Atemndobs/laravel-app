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

    /**
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
                    //change  http://127.0.0.1:3000/music/burna_boy_bank_on_it_justnaijacommp3.jpeg
                    //to  http://mage.tech:8899/storage/images/burna_boy_bank_on_it_justnaijacom.mp3
                    $imageUrl = str_replace('127.0.0.1:3000/music', "$base_url/storage/images", $song->image);
                    $song->image = $imageUrl;
                    $song->save();
                    $imageUrl = str_replace('mage.tech:8899', $base_url, $song->image);

                    try {
                        $this->info('Processing song: ' . $song->title);
                        $req = Http::get($imageUrl);

                        if ($req->successful()) {
                            $this->info('Image is valid' . $req->status());
                            $songsWithImage[] = [
                                'title' => $song->title,
                                'image' => $song->image,
                                'path' => $song->path,
                            ];
                            dump($songsWithImage);
                        } else {
                            $this->error($req->status());
                            $this->error('Image is not valid');
                            $song->image = '';
                            $song->save();
                            $songsWithoutImage[] = [
                                'title' => $song->title,
                                'image' => $song->image,
                                'path' => $song->path,
                            ];
                        }
                    } catch (\Exception $e) {
                        $songsWithoutImage[] = $song->title;
                        $this->error('Error: ' . $e->getMessage());
                        $song->image = '';
                        $song->save();
                        $songsWithoutImage[] = [
                            'title' => $song->title,
                            'image' => $song->image,
                            'path' => $song->path,
                        ];
                    }

                } else {
                    $songsWithoutImage[] = $song;
                }
            });
        }

        dump([
            'songsWithImage' => $songsWithImage,
            'songsWithoutImage' => $songsWithoutImage
        ]);
        return 0;
    }
    }
