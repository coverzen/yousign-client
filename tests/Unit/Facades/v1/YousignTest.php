<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Facades\v1;

use Coverzen\Components\YousignClient\Facades\v1\Yousign;
use Coverzen\Components\YousignClient\Libs\Soa\v1\Yousign as YousignSoaLib;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentRequest;
use Illuminate\Support\Facades\Http;

/**
 * Class YousignTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Facades\v1\Yousign
 */
final class YousignTest extends TestCase
{
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
    public function it_permits_assert_is_called(): void
    {
        Yousign::fake();

        Yousign::initiateSignature(new InitiateSignatureRequest());

        Yousign::assertIsCalled('initiateSignature');
        Http::assertSentCount(1);
    }
}
