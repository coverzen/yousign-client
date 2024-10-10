<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Libs\Soa\v1;

use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureResponse;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use function is_array;

/**
 * Class Yousign.
 */
final class Yousign extends Soa
{
    /** @var string */
    public const INITIATE_SIGNATURE_URL = 'signature_requests';

    /**
     * Http API Client for requests to Yousign.
     *
     * @var PendingRequest
     */
    private PendingRequest $apiClient;

    /**
     * Yousign SOA lib constructor.
     *
     * @psalm-suppress PossiblyNullArgument
     */
    public function __construct()
    {
        parent::__construct();

        $this->apiClient = Http::connectTimeout(Arr::get($this->config, 'connection_timeout_seconds', 0))
                               ->timeout(Arr::get($this->config, 'timeout_seconds', 0))
                               ->baseUrl(Str::finish(Arr::get($this->config, 'url'), Soa::URL_SEPARATOR))
                               ->acceptJson()
                               ->withHeader(Soa::AUTHORIZATION_HEADER, self::BEARER_PREFIX . Arr::get($this->config, 'api_key'))
                               ->retry(
                                   Arr::get($this->config, 'retry_count', 1),
                                   fn (int $attempt): int => $this->calculateBackoff($attempt),
                                   fn (Exception $exception): bool => $this->manageRetry($exception)
                               )
                               ->throw(fn (ClientResponse $response, RequestException $e) => $this->logClientFailure('Yousign api returns a wrong response', $response, $e));
    }

    /**
     * @param InitiateSignatureRequest $initiateSignatureRequest
     *
     * @return InitiateSignatureResponse
     */
    public function initiateSignature(InitiateSignatureRequest $initiateSignatureRequest): InitiateSignatureResponse
    {
        /** @var Response $response */
        $response = $this->apiClient->post(self::INITIATE_SIGNATURE_URL, $initiateSignatureRequest->toArray());

        if (!is_array($response->json())) {
            throw new RuntimeException('Yousign response is not an array.');
        }

        return new InitiateSignatureResponse($response->json());
    }
}
