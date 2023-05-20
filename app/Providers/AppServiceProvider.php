<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use SingleStore\Laravel\Connect\Connection;
use SingleStore\Laravel\Connect\Connector;
// Studio\Totem\Totem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Connection::resolverFor('singlestore', function ($connection, $database, $prefix, $config) {
            return new Connection($connection, $database, $prefix, $config);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

}
//Totem::auth(function($request) {
//    // return true / false . For e.g.
//    return \auth()->guest();
//});


//     public function register(): void
//    {
//        Connection::resolverFor('singlestore', function ($connection, $database, $prefix, $config) {
//            return new Connection($connection, $database, $prefix, $config);
//        });
//    }
//
//    public function boot()
//    {
//        $this->app->bind('db.connector.singlestore', Connector::class);
//    }
