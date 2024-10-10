<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Libs\Soa\v1;

use Coverzen\Components\YousignClient\Tests\Unit\Libs\Soa\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

/**
 * Abstract Class TestCase.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
    }
}
