<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Facades\v1;

use Coverzen\Components\YousignClient\Facades\v1\Yousign;
use Coverzen\Components\YousignClient\Libs\Soa\v1\Yousign as YousignSoaLib;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentRequest;
use Illuminate\Support\Facades\Http;
use function head;

/**
 * Class YousignTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Facades\v1\Yousign
 */
final class YousignTest extends TestCase
{
    public const SIGNATURE_ID = 'signature-id';

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Facades\v1\Yousign::getFacadeAccessor
     *
     * @return void
     */
    public function it_resolves_with_actual_class(): void
    {
        Http::fake(['*' => Http::response([])]);

        Yousign::initiateSignature(new InitiateSignatureRequest());

        Http::assertSentCount(1);
        $this->assertInstanceOf(YousignSoaLib::class, Yousign::getFacadeRoot());
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Facades\v1\Yousign::initiateSignature
     *
     * @return void
     */
    public function it_permits_should_receive_on_initiate_signature(): void
    {
        Http::fake();

        /** @var InitiateSignatureRequest $request */
        $request = new InitiateSignatureRequest();

        Yousign::shouldReceive('initiateSignature')
               ->once()
               ->with($request);

        Yousign::initiateSignature($request);

        Http::assertNothingSent();
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Facades\v1\Yousign::uploadDocument
     *
     * @return void
     */
    public function it_permits_should_receive_on_upload_document(): void
    {
        Http::fake();

        /** @var UploadDocumentRequest $request */
        $request = new UploadDocumentRequest();

        Yousign::shouldReceive('uploadDocument')
               ->once()
               ->with('fake-signature-id', $request);

        Yousign::uploadDocument('fake-signature-id', $request);

        Http::assertNothingSent();
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Facades\v1\Yousign::fake
     *
     * @return void
     */
    public function it_fakes_facade(): void
    {
        Yousign::fake();

        /** @var InitiateSignatureResponse $initiateSignatureResponse */
        $initiateSignatureResponse = Yousign::initiateSignature(new InitiateSignatureRequest());

        $this->assertInstanceOf(InitiateSignatureResponse::class, $initiateSignatureResponse);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Facades\v1\Yousign::fake
     *
     * @return void
     */
    public function it_permits_assert_initiate_signature_is_called(): void
    {
        Yousign::fake();

        Yousign::initiateSignature(new InitiateSignatureRequest());

        Yousign::assertIsCalled('initiateSignature');
        Http::assertSentCount(1);
    }

    /**
     * @test
     * @covers       \Coverzen\Components\YousignClient\Facades\v1\Yousign::fake
     *
     * @return void
     */
    public function it_permits_assert_initiate_signature_with_inspection_is_called(): void
    {
        Yousign::fake();

        /** @var InitiateSignatureRequest $request */
        $request = InitiateSignatureRequest::factory()
                                           ->make();

        Yousign::initiateSignature($request);

        Yousign::assertIsCalled(
            'initiateSignature',
            function (...$args) use ($request): bool {
                $this->assertCount(1, $args);
                $this->assertInstanceOf(InitiateSignatureRequest::class, head($args));
                $this->assertSame($request->name, head($args)->name);

                return true;
            }
        );

        Http::assertSentCount(1);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Facades\v1\Yousign::fake
     *
     * @return void
     */
    public function it_permits_assert_upload_document_is_called(): void
    {
        Yousign::fake();

        Yousign::uploadDocument(
            self::SIGNATURE_ID,
            UploadDocumentRequest::factory()
                                 ->make()
        );

        Yousign::assertIsCalled('uploadDocument');
        Http::assertSentCount(1);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Facades\v1\Yousign::fake
     *
     * @return void
     */
    public function it_permits_assert_upload_with_inspection_document_is_called(): void
    {
        Yousign::fake();

        /** @var UploadDocumentRequest $uploadDocumentRequest */
        $uploadDocumentRequest = UploadDocumentRequest::factory()
                                                      ->make();

        Yousign::uploadDocument(
            self::SIGNATURE_ID,
            $uploadDocumentRequest
        );

        Yousign::assertIsCalled(
            'uploadDocument',
            function (...$args) use ($uploadDocumentRequest): bool {
                $this->assertCount(2, $args);
                $this->assertSame(self::SIGNATURE_ID, $args[0]);

                $this->assertInstanceOf(UploadDocumentRequest::class, $args[1]);
                $this->assertSame($uploadDocumentRequest->file_name, $args[1]->file_name);

                return true;
            }
        );

        Http::assertSentCount(1);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Facades\v1\Yousign::fake
     *
     * @return void
     */
    public function it_permits_assert_add_consent_is_called(): void
    {
        Yousign::fake();

        Yousign::addConsent(
            self::SIGNATURE_ID,
            AddConsentRequest::factory()
                             ->make()
        );

        Yousign::assertIsCalled('addConsent');
        Http::assertSentCount(1);
    }
}
