<?php

namespace App\Services\Storj;

use App\Models\Song;
use App\Services\UploadService;
use Illuminate\Support\Facades\Storage;
use Storj\Uplink\Access;

class StorjUploadService extends UploadService
{
    private Access $access;
    private string $audioBucket;
    private string $imagesBucket;

    public function __construct(Access $access)
    {
        $this->access = $access;
        $this->audioBucket = 'sj://curator/raw/audio/';
        $this->imagesBucket = 'sj://curator/raw/images/';
    }

    public function storeSong(string $file_name, string $bucket = 'audio', string $column= 'path')
    {

        dd([
            'execute' => shell_exec("ls -l storage/app/public/uploads/audio"),
            'dirs' => Storage::allDirectories('public'),
            'public/uploads/audio' => Storage::allFiles('public/uploads/audio'),
        ]);
//
        $path = Storage::path('public/uploads/audio/sango_dancando_o_senta.mp3');
//        // move file to public/audio
//        rename($path, Storage::path('public/audio/temp/sango_dancando_o_senta.mp3'));
//        rename(Storage::path('public/audio/temp/sango_dancando_o_senta.mp3'), Storage::path('public/uploads/audio/sango_dancando_o_senta.mp3'));
//        // upload file to storj
//        dd($path);
//       // dd(file_get_contents($path));
        dump([
            'path' => $path,
            'asset' => asset("storage/uploads/audio/sango_dancando_o_senta.mp3"),
            'content' => fopen(($path), 'r'),
            'file_name' => $file_name,
            'bucket' => $bucket,
            'column' => $column
        ]);
        dd(Storage::allDirectories('public/'));
        $file_path = asset(Storage::url($file_name));
        //$searchQuery = str_replace("public/$bucket/", '', $file_name);
        $song = Song::query()->where($column,'like', "%$file_path%")->first();

        if ($song == null){
            $new_file_path = $this->shareUpload($file_name, $bucket);
            $existingSong = Song::query()->where($column, $new_file_path)->first();
            if ($existingSong != null){
                return $existingSong->get(['id', 'title', 'path', 'image']);
            }else{
                dump([
                    'ERROR' => $file_name,
                    'song' => $song
                ]);
                return $file_name;
            }
        }

        /** @var Song $song */
        if ($this->isSongAnalysisComplete($song)) {
            if (!$this->isFileUploaded($file_name, $bucket)){
                $this->uploadSongToStorj($file_name, $bucket);
                $new_file_path = $this->shareUpload($file_name, $bucket);
                $source = strtoupper($bucket);
                $this->{"update{$source}Path"}($song, $new_file_path);
                return $song->get(['id', 'title', 'path', 'image']);
            }
            if (!str_contains($song->path, 'https://link.storjshare.io/s')){
                $this->shareUpload($file_name, $bucket);
            }

        }

//        dump( [
//            'status' => $song->status,
//            'genre' =>$song->genre,
//            'path' => $song->path
//        ]);
        return $song->{$column};
    }

    public function isSongAnalysisComplete(Song $song): bool
    {
        $song1 = Song::query()->where('path', $song->path)->get(
            ['genre', 'analyzed', 'sad', 'aggressiveness', 'bpm', 'title', 'author', 'path', 'slug']
        );
        if (
            $song->analyzed != 1
          //  || (int)$song->genre == null
          //  || !str_contains($song->path, 'http://mage.tech:')
            || $song->sad == 0
            || $song->aggressiveness == 0
            || (int)$song->bpm == 0
            || $song->title == null
            || $song->author == 0
        ) {
//            dump([
//            'status' => 'NO - NO',
//            '$song' => json_encode($song->genre),
//            'Empty' => $song->genre == "[]",
//            'Null' => (int)$song->genre == null,
//            'Zero' => (int)$song->genre == 0,
//            'song' => $song->toArray()
//        ]);
            return false;
        }

        return true;
    }

    /**
     * @param string $fileName
     * @param string $bucket
     * @return array|string
     */
    public function uploadSongToStorj(string $fileName, string $bucket): array|string
    {
        $destination = $this->{$bucket."Bucket"};
        try {
            $filePath = Storage::path($fileName);
            $shell = shell_exec("uplink cp $filePath $destination");
            dump($shell);
            return $shell;
        }catch (\Exception $e){
            return [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'shell_results' => $shell ?? 'Upload to Storj Failed'
            ];
        }

    }

    public function isFileUploaded(string $fileName, string $bucket): bool
    {
        $name = str_replace("public/$bucket/", '', $fileName);
        $url = $this->{$bucket."Bucket"} . $name;
        $uploaded_file = shell_exec("uplink ls $url");

        dump([
            'is file upladed' => $uploaded_file
        ]);
/*        dump([
            'fileName' => $fileName,
            'name' => $name,
            'Uploaded_file' => $uploaded_file
        ]);*/
        if (str_contains($uploaded_file, $name)){
            return true;
        }
        return false;
    }

    /**
     * @param Song $song
     * @param string $new_path
     * @return Song
     */
    public function updateAudioPath(Song $song, string $new_path): Song
    {
        if (str_contains($song->path, '/storage/audio/')){
            $song->path = $new_path;
            $song->save();
            return $song;
        }
        return $song;
    }

    /**
     * @param Song $song
     * @param string $new_path
     * @return Song
     */
    public function updateImagesPath(Song $song, string $new_path): Song
    {
        dump([
            'UPDATE IMAGE' => $new_path
        ]);
        if (str_contains($song->image, '/storage/images/')){
            $song->image = $new_path;
            $song->save();
            return $song;
        }
        return $song;
    }

    /**
     * @param string $fileName
     * @param string $bucket
     * @return false|string|string[]
     */
    public function shareUpload(string $fileName, string $bucket)
    {

        $name = str_replace("public/$bucket/", '', $fileName);
        $url = $this->{$bucket."Bucket"} . $name;
        $share = shell_exec("uplink share --url $url");
        $share_results = explode("\n", $share);

        foreach ($share_results as $result) {
            if (str_contains($result, 'BROWSER URL')) continue;
            if (str_contains($result, 'URL')){
                $res = strstr($result, "https:");
                dump([
                    'shared_link' => $res
                ]);
                return $res;
            }
        }
        return [
            'error' => $share ?? 'File Does not Exist in Storj'
        ];
    }

    public function storeImage(mixed $imageFile)
    {
        if ($imageFile == "public/images/.jpg"){
            $jpg = Storage::path($imageFile);
            unlink($jpg);
        }
        return $this->storeSong($imageFile, 'images', 'image') ;
    }

    public function cleanupAudio(array $audioFiles)
    {
        foreach ($audioFiles as $audioFile) {
            /**@var Song $song */
            $song = Song::query()->where('path', 'like' , "%$audioFile%")->first();
            if (
                str_contains($song->path, "https://link.storjshare.io")
                && str_contains($song->image, 'https://link.storjshare.io')
            ){
                unlink($audioFile);
            }
        }
    }

    public function cleanupImages(array $imagesFiles)
    {
        foreach ($imagesFiles as $imageFile) {
            /**@var Song $song */
            $song = Song::query()->where('image', 'like' , "%$imageFile%")->first();
            if (
                str_contains($song->path, "https://link.storjshare.io")
                && str_contains($song->image, 'https://link.storjshare.io')
            ){
                unlink($imageFile);
            }
        }
    }
}
