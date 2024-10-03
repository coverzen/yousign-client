<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests;

use Coverzen\Components\YousignClient\YousignClientServiceProvider;
use Illuminate\Support\Facades\Config;
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
    }

    /**
     * @param $app
     *
     * @return string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            YousignClientServiceProvider::class,
        ];
    }
}
