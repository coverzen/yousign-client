<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Coverzen\Components\YousignClient\Structs\Soa\v1\SignatureRequestResponse;
use Illuminate\Support\Carbon;

/**
 * Class InitiateSignatureRequestTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Structs\Soa\v1\SignatureRequestResponse
 */
final class SignatureRequestResponseTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_instantiates(): void
    {
        /** @var SignatureRequestResponse $signatureRequestResponse */
        $signatureRequestResponse = new SignatureRequestResponse();

        $this->assertInstanceOf(SignatureRequestResponse::class, $signatureRequestResponse);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\SignatureRequestResponse::factory
     *
     * @return void
     */
    public function it_is_made_by_factory(): void
    {
        /** @var SignatureRequestResponse $signatureRequestResponse */
        $signatureRequestResponse = SignatureRequestResponse::factory()
                                                             ->make();

        $this->assertInstanceOf(SignatureRequestResponse::class, $signatureRequestResponse);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_expected_properties(): void
    {
        /** @var SignatureRequestResponse $signatureRequestResponse */
        $signatureRequestResponse = SignatureRequestResponse::factory()
                                                             ->make();

        $this->assertIsString($signatureRequestResponse->id);
        $this->assertIsString($signatureRequestResponse->source);
        $this->assertIsString($signatureRequestResponse->status);
        $this->assertIsString($signatureRequestResponse->name);
        $this->assertInstanceOf(Carbon::class, $signatureRequestResponse->created_at);
        $this->assertIsBool($signatureRequestResponse->ordered_signers);
        $this->assertIsString($signatureRequestResponse->timezone);
        $this->assertInstanceOf(Carbon::class, $signatureRequestResponse->expiration_date);
        $this->assertIsString($signatureRequestResponse->delivery_mode);
        $this->assertIsArray($signatureRequestResponse->documents);
        $this->assertIsArray($signatureRequestResponse->signers);
        $this->assertIsString($signatureRequestResponse->workspace_id);
        $this->assertIsString($signatureRequestResponse->audit_trail_locale);
        $this->assertIsBool($signatureRequestResponse->signers_allowed_to_decline);
        $this->assertIsArray($signatureRequestResponse->email_notification);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest::factory
     *
     * @return void
     */
    public function it_throws_exception_on_creation_by_factory(): void
    {
        $this->expectException(StructSaveException::class);

        SignatureRequestResponse::factory()
                                 ->create();
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest::save
     *
     * @return void
     */
    public function it_throws_exception_on_saving(): void
    {
        $this->expectException(StructSaveException::class);

        (new SignatureRequestResponse())->save();
    }
}
