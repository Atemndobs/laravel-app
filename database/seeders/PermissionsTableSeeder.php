<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     */
    public function run()
    {
        $keys = [
            [
                'key' => 'browse_admin',
                'name' => 'Browse Admin',
                'guard_name' => 'admin',
            ],
            [
              'key' => 'browse_bread',
                'name' => 'Browse Bread',
                'guard_name' => 'roles',
            ],
            [
                'key' => 'browse_database',
                'name' => 'Browse Database',
                'guard_name' => 'settings',
            ],
            [
                'key' => 'browse_media',
                'name' => 'Browse Media',
                'guard_name' => 'users',
            ],
            [
                'key' => 'browse_menu',
                'name' => 'Browse Menu',
                'guard_name' => 'menus',
            ]
        ];

        foreach ($keys as $key) {
            Permission::firstOrCreate([
                'key'        => $key['key'],
                'name'      => $key['name'],
                'guard_name'=> $key['guard_name'],
                'table_name' => 'admin',
            ]);
        }

     //   Permission::generateFor('menus');

        //Permission::generateFor('roles');

        //Permission::generateFor('users');

       // Permission::generateFor('settings');
    }
}
