<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests;

use Coverzen\Components\YousignClient\YousignClientServiceProvider;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * Abstract Class TestCase.
 */
abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('app.faker_locale', 'it_IT');
        Event::fake(ResponseReceived::class);
    }

    /**
     * @param $app
     *
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            YousignClientServiceProvider::class,
        ];
    }
}
