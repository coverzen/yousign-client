<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Libs\Soa\v1;

use Coverzen\Components\YousignClient\YousignClientServiceProvider;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use function is_array;

/**
 * Class Soa.
 */
abstract class Soa
{
    /**
     * Char separator for url construction.
     *
     * @var string
     */
    public const URL_SEPARATOR = '/';

    /** @var string */
    public const AUTHORIZATION_HEADER = 'Authorization';

    /** @var string */
    public const BEARER_PREFIX = 'Bearer ';

    /**
     * Used to calculate exponential backoff strategy in retry management.
     *
     * @var int
     */
    protected const EXPONENTIAL_BACKEOFF_BASE = 2;

    /**
     * The full config for the SOA lib.
     *
     * @var array<array-key,mixed>|mixed|null
     */
    protected mixed $config;

    /**
     * Yousign SOA lib constructor.
     */
    public function __construct()
    {
        $this->config = Config::get(YousignClientServiceProvider::CONFIG_KEY);

        if (!is_array($this->config)) {
            throw new RuntimeException('Yousign configuration is not set.');
        }
    }

    /**
     * @param int $attempt
     *
     * @psalm-suppress PossiblyNullArgument
     *
     * @return int
     */
    protected function calculateBackoff(int $attempt): int
    {
        return self::EXPONENTIAL_BACKEOFF_BASE ** ($attempt - 1) * Arr::get($this->config, 'retry_sleep', 1);
    }

    /**
     * @param Exception $exception
     *
     * @return bool
     */
    protected function manageRetry(Exception $exception): bool
    {
        return $exception instanceof ConnectionException;
    }

    /**
     * @param string $message
     * @param ClientResponse $response
     * @param RequestException $e
     *
     * @return void
     */
    protected function logClientFailure(string $message, ClientResponse $response, RequestException $e): void
    {
        Log::error(
            $message,
            [
                'host' => $response->effectiveUri()?->getHost(),
                'path' => $response->effectiveUri()?->getPath(),
                'status code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]
        );
    }
}
