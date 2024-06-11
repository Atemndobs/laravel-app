<?php

use App\Services\ElkFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'soundcloud' => [
            'driver' => 'daily',
            'path' => storage_path('logs/soundcloud.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 7,
        ],
        'spotify' => [
            'driver' => 'daily',
            'path' => storage_path('logs/spotify.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 7,
        ],
        'import' => [
            'driver' => 'daily',
            'path' => storage_path('logs/import.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 7,
        ],
        'analyze' => [
            'driver' => 'daily',
            'path' => storage_path('logs/analyze.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 7,
        ],
        'download' => [
            'driver' => 'daily',
            'path' => storage_path('logs/download.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 7,
        ],
        's3' => [
            'driver' => 'daily',
            'path' => storage_path('logs/s3.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 7,
        ],
        'stack' => [
            'driver' => 'stack',
            'channels' => [
                'single',
                'daily',
                'custom',
                'syslog'
            ],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 7,
        ],
        'elk' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'replace_placeholders' => true,
            'formatter'=>ElkFormatter::class,//The only change needed here
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
               // 'stream' => 'php://stdout',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],
        'otel' => [
            'driver' => 'monolog',
            'handler' => Monolog\Handler\StreamHandler::class,
            'level' => env('LOG_LEVEL', 'debug'),
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stdout',
            ],
            //'formatter' => Monolog\Formatter\JsonFormatter::class,
            'formatter_with' => [
                'batchMode' => Monolog\Formatter\JsonFormatter::BATCH_MODE_JSON,
                'appendNewline' => true,
            ],
            'path' => storage_path('logs/laravel.log'),
        ],
        'custom' => [
            'driver' => 'monolog',
            'handler' => Monolog\Handler\WhatFailureGroupHandler::class,
            'with' => [
                'handlers' => [
                    (new Monolog\Handler\StreamHandler('php://stdout', Monolog\Level::Debug))
                        ->setFormatter(new Monolog\Formatter\JsonFormatter()),
                    (new Monolog\Handler\RotatingFileHandler(storage_path('logs/laravel.log'), 0, Monolog\Level::Debug))
                        ->setFormatter(new Monolog\Formatter\JsonFormatter()),
                ],
            ],
            'formatter' => env('LOG_STDERR_FORMATTER'),
        ],

    ],

];
