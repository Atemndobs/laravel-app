<?php

namespace App\Console\Commands\Scraper;

use App\Models\Song;
use App\Services\Scraper\SoundcloudService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SoundcloudDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:sc {link?} {--a|artist=null} {--p|playlist=null} {--t|title=null} {--m|mixtape=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download music from Soundcloud by Link, artist name tiles or playlist';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->argument('link') === 'null') {
            $this->info('Please provide a link');
            return 0;
        }
        $link = $this->argument('link');
        try {
            // pass output to dev/null to prevent command from hanging
            // $path = env('AUDIO_PATH', '/var/www/html/storage/app/public/uploads/audio');
            $path = "/var/www/html/storage/app/public/uploads/audio";
            $webPath = $path;
            $shell = shell_exec("scdl  -l $link  2>&1");
            Log::info($shell);
            Log::info(shell_exec("ls -la $webPath"));
            $dl = explode("\n", trim($shell));
            $dl = array_filter($dl, function ($line) {
                return str_contains($line, 'Downloading');
            });

            Log::info(json_encode($dl, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            // remove "Downloading" from the line
            $dl = str_replace('Downloading', '', $dl);
            $dl = implode("\n", $dl);
            $dl = trim($dl);
            $slug = Str::slug($dl, '_');
            $this->call('move:audio');

            $songPath = $path . '/' . $slug . '.mp3';
            $this->call('song:import', [
                '--path' => $songPath,
            ]);

            unlink('/var/www/html/' . $songPath);

            $this->prepareOutputMessage($slug, $songPath, $webPath, $link);

        }catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return 0;
    }

    /**
     * @param array|string $slug
     * @param string $songPath
     * @param string $webPath
     * @param bool|array|string|null $link
     * @return void
     */
    public function prepareOutputMessage(array|string $slug, string $songPath, string $webPath, bool|array|string|null $link): void
    {
        $song = Song::query()->where('slug', $slug)->first();
        $message = [
            'slug' => $slug,
            'song_path' => $songPath,
            'webPath' => $webPath,
            'link' => $link,
            'artist' => $song->artist,
            'title' => $song->title,
            'path' => $song->path,
        ];
        $this->info(json_encode($message, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        Log::info(json_encode(['song_data' => $message], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
}
