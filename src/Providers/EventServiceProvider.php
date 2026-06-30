<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Providers;

use Coverzen\Components\YousignClient\Listeners\v1\LogRequestAndResponse;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Http\Client\Events\ResponseReceived;

/**
 * Class EventServiceProvider.
 */
final class EventServiceProvider extends ServiceProvider
{
    /** {@inheritdoc} */
    protected $listen = [
        ResponseReceived::class => [
            LogRequestAndResponse::class,
        ],
    ];
}
