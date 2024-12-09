<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Coverzen\Components\YousignClient\Structs\Soa\v1\GetConsentsResponse;

/**
 * Class GetConsentsResponseTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Structs\Soa\v1\GetConsentsResponse
 */
final class GetConsentsResponseTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_instantiates(): void
    {
        /** @var GetConsentsResponse $getConsentResponse */
        $getConsentResponse = new GetConsentsResponse();

        $this->assertInstanceOf(GetConsentsResponse::class, $getConsentResponse);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\GetConsentsResponse::factory
     *
     * @return void
     */
    public function it_is_made_by_factory(): void
    {
        /** @var GetConsentsResponse $getConsentResponse */
        $getConsentResponse = GetConsentsResponse::factory()
                                                 ->make();

        $this->assertInstanceOf(GetConsentsResponse::class, $getConsentResponse);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_expected_properties(): void
    {
        /** @var GetConsentsResponse $getConsentResponse */
        $getConsentResponse = GetConsentsResponse::factory()
                                                 ->make();

        $this->assertNotNull($getConsentResponse->data);
        $this->assertIsArray($getConsentResponse->data);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\GetConsentsResponse::factory
     *
     * @return void
     */
    public function it_throws_exception_on_creation_by_factory(): void
    {
        $this->expectException(StructSaveException::class);

        GetConsentsResponse::factory()
                           ->create();
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\GetConsentsResponse::save
     *
     * @return void
     */
    public function it_throws_exception_on_saving(): void
    {
        $this->expectException(StructSaveException::class);

        (new GetConsentsResponse())->save();
    }
}
