<?php

namespace App\Console\Commands\Storj;

use Illuminate\Console\Command;
use TCG\Voyager\Models\Setting;

class StorjCreateAccessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Access For StorJ storage bucket';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $setting = new Setting();
        $storj_api_key = env('STORJ_API_KEY');
        $storj_import_name = env('STORJ_IMPORT_NAME');
        $storj_satelite_address = env('STORJ_SATELITE_ADDRESS');
        $stork_passphrase = env('STORJ_PASSPHRASE');

        $access = \Storj\Uplink\Uplink::create()->requestAccessWithPassphrase(
            $storj_satelite_address,
            $storj_api_key,
            $stork_passphrase
        );
        $serialized = $access->serialize();
        $this->line("<fg=blue>Crated Access Grant :   $serialized  </>");

        $existing = Setting::query()->where('value', $serialized)->get();

        if ($existing){
            $results = $existing->toArray();
        }

        $setting->fill([
            'key' => 'admin.'.$storj_import_name,
            'details' => 'Serialized kex for Storj API Access',
            'display_name' => 'Access Grant',
            'group'=> 'Admin',
            'type'=> 'text',
            'value' => $serialized
        ]);
        $setting->save();
        $results = $setting->toArray();

        $this->table(['key','details',  'display_name', 'group', 'type', 'value'], [$results]);
    }
}
