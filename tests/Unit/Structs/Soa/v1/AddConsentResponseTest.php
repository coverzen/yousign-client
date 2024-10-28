<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureResponse;

/**
 * Class InitiateSignatureResponseTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentResponse
 */
final class AddConsentResponseTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_instantiates(): void
    {
        /** @var AddConsentResponse $addConsentResponse */
        $addConsentResponse = new AddConsentResponse();

        $this->assertInstanceOf(AddConsentResponse::class, $addConsentResponse);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureResponse::factory
     *
     * @return void
     */
    public function it_is_made_by_factory(): void
    {
        /** @var AddConsentResponse $addConsentResponse */
        $addConsentResponse = AddConsentResponse::factory()
                                              ->make();

        $this->assertInstanceOf(AddConsentResponse::class, $addConsentResponse);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_expected_properties(): void
    {
        /** @var AddConsentResponse $addConsentResponse */
        $addConsentResponse = AddConsentResponse::factory()
                                              ->make();

        $this->assertNotNull($addConsentResponse->type);
        $this->assertIsString($addConsentResponse->type);

        $this->assertNotNull($addConsentResponse->settings);
        $this->assertIsArray($addConsentResponse->settings);

        $this->assertNotNull($addConsentResponse->optional);
        $this->assertIsBool($addConsentResponse->optional);

        $this->assertNotNull($addConsentResponse->signer_ids);
        $this->assertIsArray($addConsentResponse->signer_ids);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_default_properties_values(): void
    {
        /** @var AddConsentResponse $addConsentResponse */
        $addConsentResponse = new AddConsentResponse();

        $this->assertInstanceOf(AddConsentResponse::class, $addConsentResponse);

        $this->assertNotNull($addConsentResponse->optional);
        $this->assertIsBool($addConsentResponse->optional);
        $this->assertFalse($addConsentResponse->optional);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureResponse::factory
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
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureResponse::save
     *
     * @return void
     */
    public function it_throws_exception_on_saving(): void
    {
        $this->expectException(StructSaveException::class);

        (new InitiateSignatureResponse())->save();
    }
}
