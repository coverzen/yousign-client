<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit;

use Coverzen\Components\YousignClient\YousignClientServiceProvider;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class YousignClientServiceProviderTest.
 */
#[CoversClass(YousignClientServiceProvider::class)]
final class YousignClientServiceProviderTest extends TestCase
{
    /**
     * @return void
     */
    #[Test]
    public function it_has_config(): void
    {
        $this->assertNotNull(Config::get(YousignClientServiceProvider::CONFIG_KEY));
        $this->assertIsArray(Config::get(YousignClientServiceProvider::CONFIG_KEY));
    }

    /**
     * @return void
     */
    #[Test]
    public function it_registers_the_log_channel(): void
    {
        $this->assertNotNull(Config::get('logging.channels.' . YousignClientServiceProvider::LOG_CHANNEL));
        $this->assertNotNull(Config::get('logging.channels.' . YousignClientServiceProvider::LOG_CHANNEL . '-local'));
    }
}
