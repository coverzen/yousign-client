<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Libs\Soa\v1;

use Coverzen\Components\YousignClient\Libs\Soa\v1\Soa;
use Coverzen\Components\YousignClient\Libs\Soa\v1\Yousign;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\SignerField;
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

    /**
     * Provides a set of properties to set null.
     *
     * @return array<string,array<string,mixed>>
     */
    public static function nullPropertiesProvider(): array
    {
        return [
            'no ordered_signers' => [
                'nullProperty' => 'ordered_signers',
            ],
            'no email_notification' => [
                'nullProperty' => 'email_notification',
            ],
        ];
    }

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
     * @dataProvider nullPropertiesProvider
     *
     * @param string $nullProperty
     *
     * @return void
     */
    public function it_initiates_a_procedure(string $nullProperty): void
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
                                                            ->make(
                                                                [
                                                                    $nullProperty => null,
                                                                ]
                                                            );

        /** @var InitiateSignatureResponse|null $actualSignatureResponse */
        $actualSignatureResponse = (new Yousign())->initiateSignature($initiateSignatureRequest);

        Http::assertSent(
            function (ClientRequest $request) use ($initiateSignatureRequest, $url, $nullProperty): bool {
                $this->assertSame(Request::METHOD_POST, $request->method());
                $this->assertSame($url, $request->url());

                $this->assertSame(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    Arr::first($request->header(Soa::AUTHORIZATION_HEADER))
                );

                $this->assertContains(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    $request->header(Soa::AUTHORIZATION_HEADER)
                );

                $this->assertArrayNotHasKey($nullProperty, $request->data());

                $this->assertArrayHasKey('name', $request->data());
                $this->assertSame($initiateSignatureRequest->name, $request->data()['name']);

                $this->assertArrayHasKey('timezone', $request->data());
                $this->assertSame($initiateSignatureRequest->timezone, $request->data()['timezone']);

                $this->assertArrayHasKey('delivery_mode', $request->data());
                $this->assertSame($initiateSignatureRequest->delivery_mode->value, $request->data()['delivery_mode']);

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
            function (ClientRequest $request) use ($url, $uploadDocumentRequest): bool {
                $this->assertSame(Request::METHOD_POST, $request->method());
                $this->assertSame($url, $request->url());

                $this->assertSame(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    Arr::first($request->header(Soa::AUTHORIZATION_HEADER))
                );

                $this->assertContains(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    $request->header(Soa::AUTHORIZATION_HEADER)
                );

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
    public function it_adds_signer(): void
    {
        /** @var string $url */
        $url = Str::finish(
            Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url'),
            Soa::URL_SEPARATOR
        ) . Yousign::INITIATE_SIGNATURE_URL
            . Soa::URL_SEPARATOR . self::SIGNATURE_ID . Soa::URL_SEPARATOR . Yousign::ADD_SIGNER_URL;

        /** @var AddSignerResponse $expectedAddSignerResponse */
        $expectedAddSignerResponse = AddSignerResponse::factory()
                                                      ->make();

        Http::fake(
            [
                $url => Http::response($expectedAddSignerResponse->toArray(), Response::HTTP_CREATED),
            ]
        );

        /** @var AddSignerRequest $addSignerRequest */
        $addSignerRequest = AddSignerRequest::factory()
                                            ->make();

        /** @var AddSignerResponse $actualAddSignerResponse */
        $actualAddSignerResponse = (new Yousign())->addSigner(self::SIGNATURE_ID, $addSignerRequest);

        Http::assertSent(
            function (ClientRequest $request) use ($addSignerRequest, $url): bool {
                $this->assertSame(Request::METHOD_POST, $request->method());
                $this->assertSame($url, $request->url());

                $this->assertSame(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    Arr::first($request->header(Soa::AUTHORIZATION_HEADER))
                );

                $this->assertContains(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    $request->header(Soa::AUTHORIZATION_HEADER)
                );

                $this->assertArrayHasKey('info', $request->data());
                $this->assertSame($addSignerRequest->info, $request->data()['info']);

                $this->assertArrayHasKey('signature_level', $request->data());
                $this->assertSame($addSignerRequest->signature_level->value, $request->data()['signature_level']);

                $this->assertArrayHasKey('fields', $request->data());
                $this->assertSame($addSignerRequest->fields, $request->data()['fields']);

                return true;
            }
        );

        $this->assertNotNull($actualAddSignerResponse);
        $this->assertInstanceOf(AddSignerResponse::class, $actualAddSignerResponse);

        $this->assertIsArray($actualAddSignerResponse->fields);

        /** @var SignerField $field */
        foreach ($actualAddSignerResponse->fields as $field) {
            $this->assertInstanceOf(SignerField::class, $field);
        }

        $this->assertSame(
            $expectedAddSignerResponse->toArray()['info'],
            $actualAddSignerResponse->toArray()['info']
        );

        $this->assertSame(
            $expectedAddSignerResponse->toArray()['signature_level'],
            $actualAddSignerResponse->toArray()['signature_level']
        );

        /** @var array<int,SignerField> $actualFields */
        $actualFields = Arr::get($actualAddSignerResponse->toArray(), 'fields');

        /**
         * @var SignerField $expectedField
         */
        foreach (Arr::get($expectedAddSignerResponse->toArray(), 'fields') as $key => $expectedField) {
            $this->assertSame(
                $expectedField->page,
                Arr::get($actualFields, $key)->page
            );
            $this->assertSame(
                $expectedField->type,
                Arr::get($actualFields, $key)->type
            );
            $this->assertSame(
                $expectedField->height,
                Arr::get($actualFields, $key)->height
            );
        }
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
