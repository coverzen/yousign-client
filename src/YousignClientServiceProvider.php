<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient;

use Coverzen\Components\YousignClient\Providers\EventServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use function array_merge;
use function config_path;

/**
 * Class YousignClientServiceProvider.
 */
final class YousignClientServiceProvider extends ServiceProvider
{
    /** @var string */
    public const CONFIG_KEY = 'yousign-client';

    /** @var string */
    public const LOG_CHANNEL = 'yousign-client';

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
     * @throws BindingResolutionException
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', self::CONFIG_KEY);
        $this->mergeLoggingChannels();
        $this->app->register(EventServiceProvider::class);
    }

    /**
     * @throws BindingResolutionException
     * @return void
     */
    private function mergeLoggingChannels(): void
    {
        /** @var array<array-key,array<array-key,mixed>> $packageLoggingConfig */
        $packageLoggingConfig = require __DIR__ . '/../config/logging.php';

        /** @var Repository $config */
        $config = $this->app->make('config');

        $config->set(
            'logging.channels',
            array_merge(
                (array) Arr::get($packageLoggingConfig, 'channels', []),
                (array) $config->get('logging.channels', [])
            )
        );
    }
}
