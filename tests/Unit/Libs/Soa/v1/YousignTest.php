<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Libs\Soa\v1;

use Coverzen\Components\YousignClient\Libs\Soa\v1\Soa;
use Coverzen\Components\YousignClient\Libs\Soa\v1\Yousign;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentResponse;
use Coverzen\Components\YousignClient\YousignClientServiceProvider;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPUnit\Framework\ExpectationFailedException;
use function in_array;

/**
 * Class YousignTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Libs\Soa\v1\Yousign
 */
final class YousignTest extends TestCase
{
    /** @var string */
    private const SIGNATURE_ID = '0da2b825-3682-4d42-bcd0-d01f791940ff';

    /** @var array<int,string> */
    private const INITIATE_PROCEDURE_REQUIRED_PROPERTIES = [
        'name',
        'delivery_mode',
        'timezone',
    ];

    /**
     * @test
     *
     * @return void
     */
    public function it_can_be_instantiated(): void
    {
        $this->assertNotNull(new Yousign());
    }

    /**
     * @test
     * @covers      ::initiateSignature
     * @return void
     */
    public function it_initiates_a_procedure(): void
    {
        /** @var string $url */
        $url = Str::finish(
            Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url'),
            Soa::URL_SEPARATOR
        ) . Yousign::INITIATE_SIGNATURE_URL;

        /** @var InitiateSignatureResponse $expectedSignatureResponse */
        $expectedSignatureResponse = InitiateSignatureResponse::factory()
                                                              ->make();

        Http::fake(
            [
                $url => Http::response($expectedSignatureResponse->toArray(), Response::HTTP_CREATED),
            ]
        );

        /** @var InitiateSignatureRequest $initiateSignatureRequest */
        $initiateSignatureRequest = InitiateSignatureRequest::factory()
                                                            ->make();

        /** @var InitiateSignatureResponse|null $actualSignatureResponse */
        $actualSignatureResponse = (new Yousign())->initiateSignature($initiateSignatureRequest);

        Http::assertSent(
            static function (ClientRequest $request) use ($initiateSignatureRequest, $url): bool {
                if ($request->method() !== Request::METHOD_POST) {
                    throw new ExpectationFailedException('Request method must be POST');
                }

                if (
                    $request->url() !== $url
                ) {
                    throw new ExpectationFailedException('Request URL must be ' . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url') . Yousign::INITIATE_SIGNATURE_URL);
                }

                if (
                    !in_array(
                        Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                        $request->header(Soa::AUTHORIZATION_HEADER),
                        true
                    )
                ) {
                    throw new ExpectationFailedException(Soa::AUTHORIZATION_HEADER . ' header missing or with wrong value.');
                }

                /** @var string $property */
                foreach (self::INITIATE_PROCEDURE_REQUIRED_PROPERTIES as $property) {
                    if (!Arr::has($request->data(), $property)) {
                        throw new ExpectationFailedException("Property {$property} missing in request payload.");
                    }

                    if (Arr::get($request->data(), $property) !== $initiateSignatureRequest->{$property}) {
                        throw new ExpectationFailedException("Property {$property} in request payload has wrong value " . Arr::get($request->data(), $property) . '.');
                    }
                }

                return true;
            }
        );

        $this->assertNotNull($actualSignatureResponse);
        $this->assertInstanceOf(InitiateSignatureResponse::class, $actualSignatureResponse);

        $this->assertSame(
            $expectedSignatureResponse->toArray(),
            $actualSignatureResponse->toArray()
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_uploads_a_document(): void
    {
        /** @var string $url */
        $url = Str::finish(
            Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url'),
            Soa::URL_SEPARATOR
        ) . Yousign::INITIATE_SIGNATURE_URL
            . Soa::URL_SEPARATOR . self::SIGNATURE_ID . Soa::URL_SEPARATOR . Yousign::UPLOAD_DOCUMENT_URL;

        /** @var UploadDocumentResponse $expectedUploadDocumentResponse */
        $expectedUploadDocumentResponse = UploadDocumentResponse::factory()
                                                                ->make();

        Http::fake(
            [
                $url => Http::response($expectedUploadDocumentResponse->toArray(), Response::HTTP_CREATED),
            ]
        );

        /** @var UploadDocumentRequest $uploadDocumentRequest */
        $uploadDocumentRequest = UploadDocumentRequest::factory()
                                                      ->make();

        /** @var UploadDocumentResponse $actualUploadDocumentResponse */
        $actualUploadDocumentResponse = (new Yousign())->uploadDocument(self::SIGNATURE_ID, $uploadDocumentRequest);

        Http::assertSent(
            static function (ClientRequest $request) use ($url, $uploadDocumentRequest): bool {
                if ($request->method() !== Request::METHOD_POST) {
                    throw new ExpectationFailedException('Request method must be POST');
                }

                if (
                    $request->url() !== $url
                ) {
                    throw new ExpectationFailedException('Request URL must be ' . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url') . Yousign::INITIATE_SIGNATURE_URL);
                }

                if (
                    !in_array(
                        Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                        $request->header(Soa::AUTHORIZATION_HEADER),
                        true
                    )
                ) {
                    throw new ExpectationFailedException(Soa::AUTHORIZATION_HEADER . ' header missing or with wrong value.');
                }

                if (
                    !Arr::first(
                        $request->header('Content-Type'),
                        static fn (string $contentType): bool => Str::startsWith($contentType, 'multipart/form-data')
                    )
                ) {
                    throw new ExpectationFailedException('Content-Type header missing multipart/form-data.');
                }

                if (
                    !Arr::first(
                        $request->data(),
                        static fn (array $item): bool => Arr::get($item, 'name') === Yousign::NATURE_PARAM
                    )
                ) {
                    throw new ExpectationFailedException("Property 'nature' missing in request payload.");
                }

                if (
                    !Arr::first(
                        $request->data(),
                        static fn (array $item): bool => Arr::get($item, 'name') === Yousign::FILE_PARAM
                    )
                ) {
                    throw new ExpectationFailedException("Property 'file' missing in request payload.");
                }

                if (
                    !Arr::first(
                        $request->data(),
                        static fn (array $item): bool => Arr::get($item, 'contents') === $uploadDocumentRequest->file_content
                    )
                ) {
                    throw new ExpectationFailedException('Wrong file content in request payload.');
                }

                if (
                    !Arr::first(
                        $request->data(),
                        static fn (array $item): bool => Arr::get($item, 'filename') === $uploadDocumentRequest->file_name
                    )
                ) {
                    throw new ExpectationFailedException('Wrong file name in request payload.');
                }

                return true;
            }
        );

        $this->assertNotNull($actualUploadDocumentResponse);
        $this->assertInstanceOf(UploadDocumentResponse::class, $actualUploadDocumentResponse);

        $this->assertSame(
            $expectedUploadDocumentResponse->toArray(),
            $actualUploadDocumentResponse->toArray()
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public static function it_add_signer()
    {
        /** @var AddSignerRequest $addSignerRequest */
        $addSignerRequest = AddSignerRequest::factory()
                                                      ->make();

        (new Yousign())->addSigner($addSignerRequest);
    }

    /**
     * Provides a set of error status code.
     *
     * @return array<array-key,array<array-key,int>>
     */
    public static function errorStatusProvider(): array
    {
        return [
            'bad request' => [
                'status' => Response::HTTP_BAD_REQUEST,
            ],
            'unauthorized' => [
                'status' => Response::HTTP_UNAUTHORIZED,
            ],
            'forbidden' => [
                'status' => Response::HTTP_FORBIDDEN,
            ],
            'not found' => [
                'status' => Response::HTTP_NOT_FOUND,
            ],
            'internal server error' => [
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ],
        ];
    }

    /**
     * @test
     * @covers       \Coverzen\Components\YousignClient\Libs\Soa\v1\Yousign::__construct
     * @dataProvider errorStatusProvider
     *
     * @param int $status
     *
     * @return void
     */
    public function it_logs_errors_on_api_client_failure(int $status): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('HTTP request returned status code ' . $status);

        Log::shouldReceive('error')
           ->once()
           ->withSomeOfArgs('Yousign api returns a wrong response');

        /** @var string $url */
        $url = Str::finish(
            Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url'),
            Soa::URL_SEPARATOR
        ) . Yousign::INITIATE_SIGNATURE_URL;

        Http::fake(
            [
                $url => Http::response([], $status),
            ]
        );

        (new Yousign())->initiateSignature(InitiateSignatureRequest::factory()->make());
    }
}
