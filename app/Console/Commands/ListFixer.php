<?php

namespace App\Console\Commands;

use App\Models\Song;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ListFixer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix {--p|path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix List';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->option('path');
        $this->info('Fixing List...');

        $files = glob($path . '/*');

        foreach ($files as $file) {
            $fileName = basename($file);
            $fileName = str_replace('.mp3', '', $fileName);
            $fileName = Str::slug($fileName, '_');
            $fileName = $fileName . '.mp3';
            // if file name changed, save new file back to original path
            if ($fileName !== basename($file)) {
                $destination = $path . '/' . $fileName;
                dump([
                    'file' => basename($file),
                    'fileName' => $fileName,
                    'destination' => $destination,
                ]);
                rename($file, $destination);
            }
        }

        dd('done !!!!!');
    }
}
