<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | This option controls the default broadcaster that will be used by the
    | framework when an event needs to be broadcast. You may set this to
    | any of the connections defined in the "connections" array below.
    |
    | Supported: "pusher", "ably", "redis", "log", "null"
    |
    */

    'default' => env('BROADCAST_DRIVER', 'pusher'),

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the broadcast connections that will be used
    | to broadcast events to other systems or over websockets. Samples of
    | each available type of connection are provided inside this array.
    |
    */

    'connections' => [
        # pusher.com
//        'pusher' => [
//            'driver' => 'pusher',
//            'key' => "2b6e66c32f76cec6c6f7",
//            'secret' => "bb62a26c8f770c1438ee",
//            'app_id' => "1631944",
//            'options' => [
//                 'cluster' => 'eu',
//                 'useTLS' => true
//            ]
//        ],
//        'pusher' => [
//            'driver' => 'pusher',
//            'key' => env('PUSHER_APP_KEY', 'app-key'),
//            'secret' => env('PUSHER_APP_SECRET', 'app-secret'),
//            'app_id' => env('PUSHER_APP_ID', 'app-id'),
//            'options' => [
//                'cluster' => env('PUSHER_APP_CLUSTER'),
//                'encrypted' => true,
//                'host' => env('PUSHER_HOST', 'websocket.curator.atemkeng.eu'),
//                'debug'=> true,
//                'port' => env('PUSHER_PORT', 80),
//                'scheme' => env('PUSHER_SCHEME', 'http')
//            ],

        'pusher' => [
            'driver' => 'pusher',
            'key' => 'app-key',
            'secret' => 'app-secret',
            'app_id' => 'app-id',
            'options' => [
                'cluster' => 'mt1',
                'encrypted' => true,
                'host' => 'websocket.curator.atemkeng.eu',
                'debug'=> true,
                'port' => 80,
                'scheme' => 'http'
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
