<?php

namespace App\Console\Commands\Song;

use App\Models\Song;
use App\Services\Storj\StorjUploadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Storj\Uplink\Uplink;

class StorjUploadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store {--a|audio} {--i|images} {--f|file_name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store / Upload All Analyzed Audio or Images to Storj Storage -a=audio folder | -i=image folder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $audio = $this->option('audio');
        $images = $this->option('images');
        $file = $this->option('file_name');

        $imageFiles = Storage::files('public/images');
        $audioFiles = Storage::files('public/audio');
//        dump([
//            'audio_path' => $audioFilesPath,
//            'audio' => $audioFiles
//        ]);

        $accessSetting = setting('admin.curator');
        $access = Uplink::create()->parseAccess($accessSetting);
        $storageService = new StorjUploadService($access);
        $songs = [];

        if ($file){
            $storageService->storeSong($file);
            $storageService->storeImage($file);
            return 0;
        }
        if ($audio){
            $this->uploadAudio($storageService, $audioFiles);
            return 0;
        }

        if ($images) {
            $this->uploadImages($storageService, $imageFiles);
            return 0;
        }

        $songs[] = $this->uploadAudio($storageService, $audioFiles);
        $songs[] = $this->uploadImages($storageService, $imageFiles);

        //$storageService->cleanupAudio($audioFiles);
        //$storageService->cleanupImages($imageFiles);

        $this->table(['id', 'title', 'path'], $songs);
        //         $this->call('index:reindex');
        //        $this->call('scout:import', ['model' => Song::class]);
        //        $this->info('Scout index updated');
    }

    public function uploadAudio($storageService, $audioFiles)
    {
        $songs = [];
        foreach ($audioFiles as $audioFile) {
            $song = $storageService->storeSong($audioFile);

            if (is_array($song)) {
                $songs[] = $song;
            }
        }
        return $songs;
    }
    public function uploadImages(StorjUploadService $storageService, array $imageFiles)
    {
        $songs = [];

        foreach ($imageFiles as $imageFile){
            $song = $storageService->storeImage($imageFile);

            if (is_array($song)){
                dump($song);
                $songs[] = $song;
            }
        }
        return $songs;
    }
}
