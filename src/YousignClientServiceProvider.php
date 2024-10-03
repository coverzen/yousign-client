<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient;

use Illuminate\Support\ServiceProvider;
use function config_path;

/**
 * Class YousignClientServiceProvider.
 */
final class YousignClientServiceProvider extends ServiceProvider
{
    /** @var string */
    public const CONFIG_KEY = 'yousign-client';

    /**
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [__DIR__ . '/../config/config.php' => config_path(self::CONFIG_KEY . '.php')],
                'config'
            );
        }
    }

    /**
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', self::CONFIG_KEY);
    }
}
