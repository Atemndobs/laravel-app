<?php

namespace App\Console\Commands\Db;

use App\Models\Song;
use http\Client\Curl\User;
use Illuminate\Console\Command;

class SongCreatedByUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cby';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $songs = Song::all();
        $user = \App\Models\AdminUser::first();
        /** @var Song $song */
        foreach ($songs as $song) {
            $song->created_by_id = $user->id;
            $song->updated_by_id = $user->id;
            $song->save();
        }
        return 0;
    }
}
