<?php

namespace App\Console\Commands\Db;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin {name?} {email?} {password?}';

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
        // create a user with admin privileges
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        if ($name === null){
            $name = 'Atem';
        }
        if($email === null) {
            $email = 'atemndobs@yahoo.com';

        }

        if ($password === null) {
            $password = 'Atem1234';
        }

        if (!$name || !$email || !$password) {
            $this->info("Creating Default Admin User : $name, $email, $password");
            return 1;
        }

        if (DB::table('users')->where('email', $email)->exists()) {
            $this->error('User already exists');
            return 1;
        }

        DB::table('users')->insert([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
        ]);
        $this->info('User created');

        return 0;
    }
}
