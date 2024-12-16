<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Libs\Soa\v1;

use Coverzen\Components\YousignClient\Fakes\v1\YousignFaker;
use Coverzen\Components\YousignClient\Libs\Soa\v1\Soa;
use Coverzen\Components\YousignClient\Libs\Soa\v1\Yousign;
use Coverzen\Components\YousignClient\Structs\Soa\v1\ActivateSignatureResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\GetAuditTrailDetailResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\GetConsentsResponse;
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
use function implode;

/**
 * Class YousignTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Libs\Soa\v1\Yousign
 */
final class YousignTest extends TestCase
{
    /** @var string */
    private const SIGNATURE_ID = '0da2b825-3682-4d42-bcd0-d01f791940ff';

    /** @var string */
    private const SIGNER_ID = '0da2b115-3682-4f42-bmn0-d90f43194ht';

    /** @var string */
    private const DOCUMENT_ID = '89120884-d29a-4b1a-ac7b-a9e73a872796';

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
        ) . Yousign::SIGNATURE_REQUESTS_BASE_URL;

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
     * Provides a set of boolean to indicate if file should be encoded or not.
     *
     * @return array<string,array<string,mixed>>
     */
    public static function encodeProvider(): array
    {
        return [
            'encoded' => [
                'encode' => true,
            ],
            'not encoded' => [
                'encode' => false,
            ],
        ];
    }

    /**
     * @test
     * @covers ::uploadDocument
     * @dataProvider encodeProvider
     *
     * @param bool $encode
     *
     * @return void
     */
    public function it_uploads_a_document(bool $encode): void
    {
        /** @var string $url */
        $url = Str::finish(
            Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url'),
            Soa::URL_SEPARATOR
        ) . implode(
            Soa::URL_SEPARATOR,
            [
                Yousign::SIGNATURE_REQUESTS_BASE_URL,
                self::SIGNATURE_ID,
                Yousign::DOCUMENT_URL,
            ]
        );

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
                                                      ->make(
                                                          [
                                                              'file_content' => $encode ? base64_encode(YousignFaker::FAKE_DOCUMENT_CONTENT) : YousignFaker::FAKE_DOCUMENT_CONTENT,
                                                          ]
                                                      );

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
                        static fn (array $item): bool => Arr::get($item, 'contents') === YousignFaker::FAKE_DOCUMENT_CONTENT
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
        ) . Yousign::SIGNATURE_REQUESTS_BASE_URL
            . Soa::URL_SEPARATOR . self::SIGNATURE_ID . Soa::URL_SEPARATOR . Yousign::SIGNER_URL;

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
                $this->assertNotNull($addSignerRequest->signature_level);
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

        /** @var array<int, SignerField> $expectedFields */
        $expectedFields = Arr::get($expectedAddSignerResponse->toArray(), 'fields');

        /**
         * @var int $key
         * @var SignerField $expectedField
         */
        foreach ($expectedFields as $key => $expectedField) {
            /** @var SignerField $actualField */
            $actualField = Arr::get($actualFields, $key);

            $this->assertSame(
                $expectedField->page,
                $actualField->page
            );
            $this->assertSame(
                $expectedField->type,
                $actualField->type
            );
            $this->assertSame(
                $expectedField->height,
                $actualField->height
            );
        }
    }

    /**
     * @test
     * @covers ::addConsent
     *
     * @return void
     */
    public function it_adds_consent(): void
    {
        /** @var string $url */
        $url = Str::finish(
            Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url'),
            Soa::URL_SEPARATOR
        ) . Yousign::SIGNATURE_REQUESTS_BASE_URL
            . Soa::URL_SEPARATOR . self::SIGNATURE_ID . Soa::URL_SEPARATOR . Yousign::ADD_CONSENT_URL;

        /** @var AddConsentResponse $expectedAddConsentResponse */
        $expectedAddConsentResponse = AddConsentResponse::factory()
                                                        ->make();

        Http::fake(
            [
                $url => Http::response($expectedAddConsentResponse->toArray(), Response::HTTP_CREATED),
            ]
        );

        /** @var AddConsentRequest $addConsentRequest */
        $addConsentRequest = AddConsentRequest::factory()
                                              ->make();

        /** @var AddConsentResponse|null $actualAddConsentResponse */
        $actualAddConsentResponse = (new Yousign())->addConsent(self::SIGNATURE_ID, $addConsentRequest);

        Http::assertSent(
            function (ClientRequest $request) use ($addConsentRequest, $url): bool {
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

                $this->assertArrayHasKey('type', $request->data());
                $this->assertSame($addConsentRequest->type, $request->data()['type']);

                $this->assertArrayHasKey('settings', $request->data());
                $this->assertSame($addConsentRequest->settings, $request->data()['settings']);

                $this->assertArrayHasKey('optional', $request->data());
                $this->assertSame($addConsentRequest->optional, $request->data()['optional']);

                return true;
            }
        );

        $this->assertNotNull($actualAddConsentResponse);
        $this->assertInstanceOf(AddConsentResponse::class, $actualAddConsentResponse);

        $this->assertSame(
            $expectedAddConsentResponse->toArray(),
            $actualAddConsentResponse->toArray()
        );
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
     * @covers       ::__construct
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
        ) . Yousign::SIGNATURE_REQUESTS_BASE_URL;

        Http::fake(
            [
                $url => Http::response([], $status),
            ]
        );

        (new Yousign())->initiateSignature(InitiateSignatureRequest::factory()->make());
    }

    /**
     * @test
     * @covers      ::activateSignatureRequest
     *
     * @return void
     */
    public function it_activates_a_signature_request(): void
    {
        /** @var string $url */
        $url = Str::finish(
            Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url'),
            Soa::URL_SEPARATOR
        ) . Yousign::SIGNATURE_REQUESTS_BASE_URL . Soa::URL_SEPARATOR . self::SIGNATURE_ID . Soa::URL_SEPARATOR . Yousign::ACTIVATE_SIGNATURE_URL;

        /** @var ActivateSignatureResponse $expectedActivateSignatureResponse */
        $expectedActivateSignatureResponse = ActivateSignatureResponse::factory()
                                                                      ->make();

        Http::fake(
            [
                $url => Http::response($expectedActivateSignatureResponse->toArray(), Response::HTTP_CREATED),
            ]
        );

        /** @var ActivateSignatureResponse|null $actualActivateSignatureResponse */
        $actualActivateSignatureResponse = (new Yousign())->activateSignature(self::SIGNATURE_ID);

        Http::assertSent(
            function (ClientRequest $request) use ($url): bool {
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

                return true;
            }
        );

        $this->assertNotNull($actualActivateSignatureResponse);
        $this->assertInstanceOf(ActivateSignatureResponse::class, $actualActivateSignatureResponse);

        $this->assertSame(
            $expectedActivateSignatureResponse->id,
            $actualActivateSignatureResponse->id
        );

        $this->assertSame(
            $expectedActivateSignatureResponse->type,
            $actualActivateSignatureResponse->type
        );

        $this->assertCount(
            count($expectedActivateSignatureResponse->signers),
            $actualActivateSignatureResponse->signers
        );
    }

    /**
     * @test
     * @covers      ::getSignatureById
     *
     * @return void
     */
    public function it_gets_signature_by_id(): void
    {
        /** @var string $url */
        $url = Str::finish(
            Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url'),
            Soa::URL_SEPARATOR
        ) . Yousign::SIGNATURE_REQUESTS_BASE_URL . Soa::URL_SEPARATOR . self::SIGNATURE_ID;

        /** @var InitiateSignatureResponse $expectedSignatureResponse */
        $expectedSignatureResponse = InitiateSignatureResponse::factory()
                                                              ->make();

        Http::fake(
            [
                $url => Http::response($expectedSignatureResponse->toArray(), Response::HTTP_CREATED),
            ]
        );

        /** @var InitiateSignatureResponse|null $actualSignatureResponse */
        $actualSignatureResponse = (new Yousign())->getSignatureById(self::SIGNATURE_ID);

        Http::assertSent(
            function (ClientRequest $request) use ($url): bool {
                $this->assertSame(Request::METHOD_GET, $request->method());
                $this->assertSame($url, $request->url());

                $this->assertSame(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    Arr::first($request->header(Soa::AUTHORIZATION_HEADER))
                );

                $this->assertContains(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    $request->header(Soa::AUTHORIZATION_HEADER)
                );

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
     * @covers      ::getDocumentById
     *
     * @return void
     */
    public function it_gets_document_by_id(): void
    {
        /** @var string $url */
        $url = Str::finish(
            Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url'),
            Soa::URL_SEPARATOR
        ) . implode(
            Soa::URL_SEPARATOR,
            [
                Yousign::SIGNATURE_REQUESTS_BASE_URL,
                self::SIGNATURE_ID,
                Yousign::DOCUMENT_URL,
                self::DOCUMENT_ID,
                Yousign::DOWNLOAD_URL,
            ]
        );

        Http::fake(
            [
                $url => Http::response(YousignFaker::FAKE_DOCUMENT_CONTENT, Response::HTTP_CREATED),
            ]
        );

        /** @var string|null $actualDownloadDocumentResponse */
        $actualDownloadDocumentResponse = (new Yousign())->getDocumentById(self::SIGNATURE_ID, self::DOCUMENT_ID);

        Http::assertSent(
            function (ClientRequest $request) use ($url): bool {
                $this->assertSame(Request::METHOD_GET, $request->method());
                $this->assertSame($url, $request->url());

                $this->assertSame(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    Arr::first($request->header(Soa::AUTHORIZATION_HEADER))
                );

                $this->assertContains(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    $request->header(Soa::AUTHORIZATION_HEADER)
                );

                return true;
            }
        );

        $this->assertNotNull($actualDownloadDocumentResponse);
        $this->assertIsString($actualDownloadDocumentResponse);

        $this->assertSame(
            YousignFaker::FAKE_DOCUMENT_CONTENT,
            $actualDownloadDocumentResponse
        );
    }

    /**
     * @test
     * @covers      ::getAuditTrail
     *
     * @return void
     */
    public function it_gets_audit_trail(): void
    {
        /** @var string $url */
        $url = Str::finish(
            Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url'),
            Soa::URL_SEPARATOR
        ) . Yousign::SIGNATURE_REQUESTS_BASE_URL . Soa::URL_SEPARATOR . self::SIGNATURE_ID . Soa::URL_SEPARATOR . Yousign::SIGNER_URL . Soa::URL_SEPARATOR . self::SIGNER_ID . Soa::URL_SEPARATOR . Yousign::DOWNLOAD_AUDIT_TRAIL;

        /** @var string $expectedDownloadAuditTrailResponse */
        $expectedDownloadAuditTrailResponse = YousignFaker::FAKE_DOCUMENT_CONTENT;

        Http::fake(
            [
                $url => Http::response($expectedDownloadAuditTrailResponse, Response::HTTP_CREATED),
            ]
        );

        /** @var string|null $actualDownloadAuditTrailResponse */
        $actualDownloadAuditTrailResponse = (new Yousign())->getAuditTrail(self::SIGNATURE_ID, self::SIGNER_ID);

        Http::assertSent(
            function (ClientRequest $request) use ($url): bool {
                $this->assertSame(Request::METHOD_GET, $request->method());
                $this->assertSame($url, $request->url());

                $this->assertSame(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    Arr::first($request->header(Soa::AUTHORIZATION_HEADER))
                );

                $this->assertContains(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    $request->header(Soa::AUTHORIZATION_HEADER)
                );

                return true;
            }
        );

        $this->assertNotNull($actualDownloadAuditTrailResponse);
        $this->assertIsString($actualDownloadAuditTrailResponse);

        $this->assertSame(
            $expectedDownloadAuditTrailResponse,
            $actualDownloadAuditTrailResponse
        );
    }

    /**
     * @test
     * @covers      ::getConsentsById
     *
     * @return void
     */
    public function it_gets_consents_by_id(): void
    {
        /** @var string $url */
        $url = Str::finish(
            Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url'),
            Soa::URL_SEPARATOR
        ) . implode(
            Soa::URL_SEPARATOR,
            [
                Yousign::SIGNATURE_REQUESTS_BASE_URL,
                self::SIGNATURE_ID,
                Yousign::ADD_CONSENT_URL,
            ]
        );

        /** @var GetConsentsResponse $expectedGetConsentsResponse */
        $expectedGetConsentsResponse = GetConsentsResponse::factory()
                                                          ->make();

        Http::fake(
            [
                $url => Http::response($expectedGetConsentsResponse->toArray(), Response::HTTP_CREATED),
            ]
        );

        /** @var GetConsentsResponse $actualGetConsentsResponse */
        $actualGetConsentsResponse = (new Yousign())->getConsentsById(self::SIGNATURE_ID);

        Http::assertSent(
            function (ClientRequest $request) use ($url): bool {
                $this->assertSame(Request::METHOD_GET, $request->method());
                $this->assertSame($url, $request->url());

                $this->assertSame(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    Arr::first($request->header(Soa::AUTHORIZATION_HEADER))
                );

                $this->assertContains(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    $request->header(Soa::AUTHORIZATION_HEADER)
                );

                return true;
            }
        );

        $this->assertNotNull($actualGetConsentsResponse);
        $this->assertInstanceOf(GetConsentsResponse::class, $actualGetConsentsResponse);

        $this->assertSame(
            $expectedGetConsentsResponse->toArray(),
            $actualGetConsentsResponse->toArray()
        );
    }

    /**
     * @test
     * @covers      ::getAuditTrailDetail
     *
     * @return void
     */
    public function it_gets_audit_trail_detail(): void
    {
        /** @var string $url */
        $url = Str::finish(
            Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url'),
            Soa::URL_SEPARATOR
        ) . implode(
            Soa::URL_SEPARATOR,
            [
                Yousign::SIGNATURE_REQUESTS_BASE_URL,
                self::SIGNATURE_ID,
                Yousign::SIGNER_URL,
                self::SIGNER_ID,
                Yousign::GET_AUDIT_TRAIL_DETAIL,
            ]
        );

        /** @var GetAuditTrailDetailResponse $expectedGetAuditTrailDetailResponse */
        $expectedGetAuditTrailDetailResponse = GetAuditTrailDetailResponse::factory()
                                                                          ->make();

        Http::fake(
            [
                $url => Http::response($expectedGetAuditTrailDetailResponse->toArray(), Response::HTTP_CREATED),
            ]
        );

        /** @var GetAuditTrailDetailResponse $actualGetAuditTrailDetailResponse */
        $actualGetAuditTrailDetailResponse = (new Yousign())->getAuditTrailDetail(self::SIGNATURE_ID, self::SIGNER_ID);

        Http::assertSent(
            function (ClientRequest $request) use ($url): bool {
                $this->assertSame(Request::METHOD_GET, $request->method());
                $this->assertSame($url, $request->url());

                $this->assertSame(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    Arr::first($request->header(Soa::AUTHORIZATION_HEADER))
                );

                $this->assertContains(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    $request->header(Soa::AUTHORIZATION_HEADER)
                );

                return true;
            }
        );

        $this->assertNotNull($actualGetAuditTrailDetailResponse);
        $this->assertInstanceOf(GetAuditTrailDetailResponse::class, $actualGetAuditTrailDetailResponse);

        $this->assertSame(
            $expectedGetAuditTrailDetailResponse->toArray(),
            $actualGetAuditTrailDetailResponse->toArray()
        );
    }

    /**
     * @test
     * @covers      ::deleteSignatureRequest
     *
     * @return void
     */
    public function it_deletes_signature_request(): void
    {
        /** @var string $url */
        $url = Str::finish(
            Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url'),
            Soa::URL_SEPARATOR
        ) . implode(
            Soa::URL_SEPARATOR,
            [
                Yousign::SIGNATURE_REQUESTS_BASE_URL,
                self::SIGNATURE_ID,
            ]
        ) . Yousign::PERMANENT_DELETED_SIGNATURE_PARAMS;

        Http::fake(
            [
                $url => Http::response(null, Response::HTTP_NO_CONTENT),
            ]
        );

        /** @var bool $deleteSignatureResponse */
        $deleteSignatureResponse = (new Yousign())->deleteSignatureRequest(self::SIGNATURE_ID);

        Http::assertSent(
            function (ClientRequest $request) use ($url): bool {
                $this->assertSame(Request::METHOD_DELETE, $request->method());
                $this->assertSame($url, $request->url());

                $this->assertSame(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    Arr::first($request->header(Soa::AUTHORIZATION_HEADER))
                );

                $this->assertContains(
                    Yousign::BEARER_PREFIX . Config::get(YousignClientServiceProvider::CONFIG_KEY . '.api_key'),
                    $request->header(Soa::AUTHORIZATION_HEADER)
                );

                return true;
            }
        );

        $this->assertNotNull($deleteSignatureResponse);
        $this->assertTrue($deleteSignatureResponse);
    }
}
