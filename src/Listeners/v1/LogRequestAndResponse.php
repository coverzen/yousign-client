<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Listeners\v1;

use Coverzen\Components\YousignClient\YousignClientServiceProvider;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use function array_keys;
use function explode;
use function in_array;

/**
 * Class LogRequestAndResponse.
 */
final class LogRequestAndResponse
{
    /** @var string */
    public const REQUEST_LOG_MESSAGE = 'Request:  ';

    /** @var string */
    public const REDACTED = '***REDACTED***';

    /**
     * Headers redacted from the logged context because they carry secrets
     * (e.g. the Yousign `Authorization: Bearer <API key>` header).
     *
     * @var array<int,string>
     */
    private const SENSITIVE_HEADERS = [
        'authorization',
    ];

    /**
     * Handle the event.
     *
     * @param ResponseReceived $event
     *
     * @throws RuntimeException
     */
    public function handle(ResponseReceived $event): void
    {
        if (!Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url')) {
            return;
        }

        /** @var string $expectedHost */
        $expectedHost = Arr::get(
            explode(
                '//',
                Config::string(YousignClientServiceProvider::CONFIG_KEY . '.url')
            ),
            1,
            ''
        );

        /** @var string $actualHost */
        foreach ($event->request->header('Host') as $actualHost) {
            if (!Str::startsWith($expectedHost, $actualHost)) {
                return;
            }
        }

        /** @var RequestInterface $request */
        $request = $event->request->toPsrRequest();

        /** @var ResponseInterface $response */
        $response = $event->response->toPsrResponse();

        /** @var string $body */
        $body = $response->getBody()->getContents();

        /**
         * Rewind the request body stream before reading it.
         *
         * @var StreamInterface $requestBodyStream
         */
        $requestBodyStream = $request->getBody();
        $requestBodyStream->rewind();

        /** @var non-falsy-string $message */
        $message = self::REQUEST_LOG_MESSAGE . $request->getUri()->getPath();

        /** @var array<array-key,array<array-key,mixed>> $context */
        $context = [
            'request' => [
                'protocol_version' => $request->getProtocolVersion(),
                'method' => $request->getMethod(),
                'scheme' => $request->getUri()->getScheme(),
                'host' => $request->getUri()->getHost(),
                'port' => $request->getUri()->getPort(),
                'path' => $request->getUri()->getPath(),
                'authority' => $request->getUri()->getAuthority(),
                'query' => $request->getUri()->getQuery(),
                'fragment' => $request->getUri()->getFragment(),
                'user_info' => $request->getUri()->getUserInfo(),
                'headers' => $this->redactHeaders($request->getHeaders()),
                'request_target' => $request->getRequestTarget(),
                'body' => $requestBodyStream->getContents(),
            ],
            'response' => [
                'protocol' => $response->getProtocolVersion(),
                'status_code' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => $body,
                'reason_phrase' => $response->getReasonPhrase(),
            ],
        ];

        if ($response->getStatusCode() < Response::HTTP_MULTIPLE_CHOICES) {
            Log::channel(YousignClientServiceProvider::LOG_CHANNEL)
               ->info($message, $context);

            return;
        }

        if ($response->getStatusCode() < Response::HTTP_BAD_REQUEST) {
            Log::channel(YousignClientServiceProvider::LOG_CHANNEL)
               ->notice($message, $context);

            return;
        }

        if ($response->getStatusCode() < Response::HTTP_INTERNAL_SERVER_ERROR) {
            Log::channel(YousignClientServiceProvider::LOG_CHANNEL)
               ->warning($message, $context);

            return;
        }

        Log::channel(YousignClientServiceProvider::LOG_CHANNEL)
           ->error($message, $context);
    }

    /**
     * Redact sensitive headers (e.g. the Yousign Bearer API key) before logging.
     *
     * @param array<array-key,array<array-key,string>> $headers
     *
     * @return array<array-key,array<array-key,string>>
     */
    private function redactHeaders(array $headers): array
    {
        /** @var array-key $name */
        foreach (array_keys($headers) as $name) {
            if (in_array(Str::lower((string) $name), self::SENSITIVE_HEADERS, true)) {
                $headers[$name] = [self::REDACTED];
            }
        }

        return $headers;
    }
}
