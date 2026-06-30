<?php declare(strict_types=1);

use Coverzen\Components\YousignClient\YousignClientServiceProvider;
use Illuminate\Support\Facades\App;

return [
    /*
     |--------------------------------------------------------------------------
     | Yousign Client Log Channels
     |--------------------------------------------------------------------------
     |
     | Custom logging channels for Yousign client operations.
     |
     */

    'channels' => [
        YousignClientServiceProvider::LOG_CHANNEL => [
            'driver' => 'stack',
            'channels' => explode(
                ',',
                (string) env('YOUSIGN_LOG_STACK', YousignClientServiceProvider::LOG_CHANNEL . '-local')
            ),
            'ignore_exceptions' => false,
            'name' => YousignClientServiceProvider::LOG_CHANNEL . '-' . App::environment(),
        ],

        YousignClientServiceProvider::LOG_CHANNEL . '-local' => [
            'driver' => 'daily',
            'path' => storage_path('logs/yousign-client.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],
    ],
];
