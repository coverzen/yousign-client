<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Listeners\v1;

use Coverzen\Components\YousignClient\Libs\Soa\v1\Soa;
use Coverzen\Components\YousignClient\Listeners\v1\LogRequestAndResponse;
use Coverzen\Components\YousignClient\YousignClientServiceProvider;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Request as IlluminateRequest;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class LogRequestAndResponseTest.
 */
#[CoversClass(LogRequestAndResponse::class)]
final class LogRequestAndResponseTest extends TestCase
{
    /** @var string */
    private const API_KEY = 'super-secret-api-key';

    /**
     * @return array<string,array<string,int|string>>
     */
    public static function responsesStatusProvider(): array
    {
        return [
            'ok' => [
                'statusCode' => IlluminateResponse::HTTP_OK,
                'logFunction' => 'info',
            ],
            'redirect' => [
                'statusCode' => IlluminateResponse::HTTP_MULTIPLE_CHOICES,
                'logFunction' => 'notice',
            ],
            'bad request' => [
                'statusCode' => IlluminateResponse::HTTP_BAD_REQUEST,
                'logFunction' => 'warning',
            ],
            'server error' => [
                'statusCode' => IlluminateResponse::HTTP_SERVICE_UNAVAILABLE,
                'logFunction' => 'error',
            ],
        ];
    }

    /**
     * @return void
     */
    #[Test]
    public function it_listens_to_event(): void
    {
        Event::assertListening(ResponseReceived::class, LogRequestAndResponse::class);
    }

    /**
     * @param int $statusCode
     * @param string $logFunction
     *
     * @return void
     */
    #[Test]
    #[DataProvider('responsesStatusProvider')]
    public function it_logs_request_and_response(int $statusCode, string $logFunction): void
    {
        /** @var Request $request */
        $request = new Request(IlluminateRequest::METHOD_POST, Config::string(YousignClientServiceProvider::CONFIG_KEY . '.url'));

        Log::expects("channel->{$logFunction}")
           ->once()
           ->withSomeOfArgs(LogRequestAndResponse::REQUEST_LOG_MESSAGE . $request->getUri()->getPath());

        (new LogRequestAndResponse())->handle(
            new ResponseReceived(
                new ClientRequest($request),
                new ClientResponse(new Response($statusCode))
            )
        );
    }

    /**
     * @param int $statusCode
     * @param string $logFunction
     *
     * @return void
     */
    #[Test]
    #[DataProvider('responsesStatusProvider')]
    public function it_doesnt_log_request_and_response_for_http_calls_to_a_different_endpoint(int $statusCode, string $logFunction): void
    {
        Log::expects("channel->{$logFunction}")
           ->never();

        (new LogRequestAndResponse())->handle(
            new ResponseReceived(
                new ClientRequest(new Request(IlluminateRequest::METHOD_POST, 'https://other-url.com')),
                new ClientResponse(new Response($statusCode))
            )
        );
    }

    /**
     * @return void
     */
    #[Test]
    public function it_redacts_the_authorization_header(): void
    {
        Log::expects('channel->info')
           ->once()
           ->withArgs(function (string $message, array $context): bool {
               /** @var array<string,array<int,string>> $headers */
               $headers = Arr::get($context, 'request.headers', []);

               $this->assertSame(
                   [LogRequestAndResponse::REDACTED],
                   Arr::get($headers, Soa::AUTHORIZATION_HEADER)
               );

               return true;
           });

        (new LogRequestAndResponse())->handle(
            new ResponseReceived(
                new ClientRequest(
                    new Request(
                        IlluminateRequest::METHOD_POST,
                        Config::string(YousignClientServiceProvider::CONFIG_KEY . '.url'),
                        [
                            Soa::AUTHORIZATION_HEADER => Soa::BEARER_PREFIX . self::API_KEY,
                        ]
                    )
                ),
                new ClientResponse(new Response(IlluminateResponse::HTTP_OK))
            )
        );
    }
}
