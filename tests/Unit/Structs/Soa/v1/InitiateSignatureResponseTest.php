<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureResponse;
use Illuminate\Support\Carbon;

/**
 * Class InitiateSignatureRequestTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureResponse
 */
final class InitiateSignatureResponseTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_instantiates(): void
    {
        /** @var InitiateSignatureResponse $initiateSignatureResponse */
        $initiateSignatureResponse = new InitiateSignatureResponse();

        $this->assertInstanceOf(InitiateSignatureResponse::class, $initiateSignatureResponse);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureResponse::factory
     *
     * @return void
     */
    public function it_is_made_by_factory(): void
    {
        /** @var InitiateSignatureResponse $initiateSignatureResponse */
        $initiateSignatureResponse = InitiateSignatureResponse::factory()
                                                              ->make();

        $this->assertInstanceOf(InitiateSignatureResponse::class, $initiateSignatureResponse);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_expected_properties(): void
    {
        /** @var InitiateSignatureResponse $initiateSignatureResponse */
        $initiateSignatureResponse = InitiateSignatureResponse::factory()
                                                              ->make();

        $this->assertIsString($initiateSignatureResponse->id);
        $this->assertIsString($initiateSignatureResponse->source);
        $this->assertIsString($initiateSignatureResponse->status);
        $this->assertIsString($initiateSignatureResponse->name);
        $this->assertInstanceOf(Carbon::class, $initiateSignatureResponse->created_at);
        $this->assertIsBool($initiateSignatureResponse->ordered_signers);
        $this->assertIsString($initiateSignatureResponse->timezone);
        $this->assertInstanceOf(Carbon::class, $initiateSignatureResponse->expiration_date);
        $this->assertIsString($initiateSignatureResponse->delivery_mode);
        $this->assertIsArray($initiateSignatureResponse->documents);
        $this->assertIsArray($initiateSignatureResponse->signers);
        $this->assertIsString($initiateSignatureResponse->workspace_id);
        $this->assertIsString($initiateSignatureResponse->audit_trail_locale);
        $this->assertIsBool($initiateSignatureResponse->signers_allowed_to_decline);
        $this->assertIsArray($initiateSignatureResponse->email_notification);
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

        InitiateSignatureResponse::factory()
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

        (new InitiateSignatureResponse())->save();
    }
}
