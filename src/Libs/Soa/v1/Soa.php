<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Libs\Soa\v1;

use Coverzen\Components\YousignClient\YousignClientServiceProvider;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Throwable;

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
     * @param int $attempt
     *
     * @return int
     */
    protected function calculateBackoff(int $attempt): int
    {
        return self::EXPONENTIAL_BACKEOFF_BASE ** ($attempt - 1) * Config::integer(YousignClientServiceProvider::CONFIG_KEY . '.retry_sleep');
    }

    /**
     * @param Throwable $exception
     *
     * @return bool
     */
    protected function manageRetry(Throwable $exception): bool
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
