<?php

namespace App\Console\Commands\Song;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanUpAuthorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:clean-up-author';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $songs = \App\Models\Song::all();

        /** @var \App\Models\Song $song */
        foreach ($songs as $song) {
            $author = $song->author;
            $title = $song->title;
            $slug = $song->slug;
            $placeHolder = str_replace('_', ' ', $slug);
            $placeHolder = ucwords($placeHolder);
            if ($author == null) {
                $song->author = $placeHolder;
                $song->save();
                continue;
            }
            if ($title == null) {
                $song->title = $placeHolder;
                $song->save();
                continue;
            }
            // if title starts with - or with /, remove it
            if (substr($title, 0, 1) == '-' || substr($title, 0, 1) == '/') {
                $title = substr($title, 1);
                $song->title = $title;
                $song->save();
            }

            // if the author's special characters are encoded, decode them

              $author = str_replace('\/', ', ', $author);
                $author = str_replace('[', '', $author);
                $author = str_replace(']', '', $author);
                $author = str_replace('"', '', $author);
                $song->author = $author;
                $song->save();
            $message = [
                'before' => $song->author,
                'after' => $author,
            ];
            $this->info(json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            Log::info(json_encode($message , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
}
