<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Coverzen\Components\YousignClient\Structs\Soa\v1\GetAuditTrailDetailResponse;

/**
 * Class GetAuditTrailDetailResponseTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Structs\Soa\v1\GetAuditTrailDetailResponse
 */
final class GetAuditTrailDetailResponseTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_instantiates(): void
    {
        /** @var GetAuditTrailDetailResponse $getConsentResponse */
        $getConsentResponse = new GetAuditTrailDetailResponse();

        $this->assertInstanceOf(GetAuditTrailDetailResponse::class, $getConsentResponse);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\GetAuditTrailDetailResponse::factory
     *
     * @return void
     */
    public function it_is_made_by_factory(): void
    {
        /** @var GetAuditTrailDetailResponse $getConsentResponse */
        $getConsentResponse = GetAuditTrailDetailResponse::factory()
                                                         ->make();

        $this->assertInstanceOf(GetAuditTrailDetailResponse::class, $getConsentResponse);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_expected_properties(): void
    {
        /** @var GetAuditTrailDetailResponse $getConsentResponse */
        $getConsentResponse = GetAuditTrailDetailResponse::factory()
                                                         ->make();

        $this->assertNotNull($getConsentResponse->version);
        $this->assertIsArray($getConsentResponse->signature_request);
        $this->assertIsArray($getConsentResponse->sender);
        $this->assertIsArray($getConsentResponse->signer);
        $this->assertIsArray($getConsentResponse->documents);
        $this->assertIsArray($getConsentResponse->organization);
        $this->assertIsArray($getConsentResponse->authentication);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\GetAuditTrailDetailResponse::factory
     *
     * @return void
     */
    public function it_throws_exception_on_creation_by_factory(): void
    {
        $this->expectException(StructSaveException::class);

        GetAuditTrailDetailResponse::factory()
                                   ->create();
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\GetAuditTrailDetailResponse::save
     *
     * @return void
     */
    public function it_throws_exception_on_saving(): void
    {
        $this->expectException(StructSaveException::class);

        (new GetAuditTrailDetailResponse())->save();
    }
}
