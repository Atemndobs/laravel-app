<?php

namespace App\Console\Commands\Analysis;

use App\Services\MoodAnalysisService;
use Illuminate\Console\Command;

class ClassifySongsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'song:classify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk Classify Songs | Classify All un-analyzed Songs ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('rabbitmq:queue-delete', ['name' => 'classify']);
        $unClassified = (new MoodAnalysisService())->classifySongs();

        $this->output->info('Queued tracks');
        $headers = [
            'number',
            'title',
            'status',
        ];

        $data = [];
        foreach ($unClassified as $title) {
            $data[] = [
                'num.' => count($data) +1,
                'title' => $title,
                'status' => 'imported',
            ];
            info("$title : has been imported");
        }

        $this->output->table($headers, $data);
        $this->info("Unclassified : " . count($unClassified));
        info('=========================================CLASSIFY_COMPLETE====================================');

        return 'job has been queued';
    }
}
