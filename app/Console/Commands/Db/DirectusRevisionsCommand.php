<?php

namespace App\Console\Commands\Db;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DirectusRevisionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clear-revisions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Directus revisions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // get count of all records in the directus_revisions table
        $count = \DB::table('directus_revisions')->count();
        // get the directus_revisions table in database and truncate it
        try {
            \DB::table('directus_revisions')->truncate();
            $message = [
                'message' => 'Directus revisions table truncated',
                'action' => 'delete ' . $count . ' records'
            ];
        } catch (\Exception $e) {
            $message = [
                'message' => 'Directus revisions was not successfully truncated',
                'action' => $e->getMessage()
            ];
        }
        $this->info(json_encode($message, JSON_PRETTY_PRINT));
        Log::warning(json_encode($message, JSON_PRETTY_PRINT));
    }
}
