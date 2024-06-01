<?php

namespace App\Providers;

use App\Services\RetryableS3Client;
use Aws\S3\S3Client;
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

        // Register the RetryableS3Client service
//        $this->app->singleton(RetryableS3Client::class, function ($app) {
//            $s3Client = new S3Client([
//                'version' => 'latest',
//                'region'  => env('AWS_DEFAULT_REGION'),
//                'credentials' => [
//                    'key'    => env('AWS_ACCESS_KEY_ID'),
//                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
//                ],
//            ]);
//
//            return new RetryableS3Client($s3Client);
//        });
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
