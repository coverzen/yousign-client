<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentRequest;

/**
 * Class AddConsentRequestTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentRequest
 */
final class AddConsentRequestTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_instantiates(): void
    {
        /** @var AddConsentRequest $addConsentRequest */
        $addConsentRequest = new AddConsentRequest();

        $this->assertInstanceOf(AddConsentRequest::class, $addConsentRequest);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentRequest::factory
     *
     * @return void
     */
    public function it_is_made_by_factory(): void
    {
        /** @var AddConsentRequest $addConsentRequest */
        $addConsentRequest = AddConsentRequest::factory()
                                              ->make();

        $this->assertInstanceOf(AddConsentRequest::class, $addConsentRequest);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_expected_properties(): void
    {
        /** @var AddConsentRequest $addConsentRequest */
        $addConsentRequest = AddConsentRequest::factory()
                                              ->make();

        $this->assertNotNull($addConsentRequest->type);
        $this->assertIsString($addConsentRequest->type);

        $this->assertNotNull($addConsentRequest->settings);
        $this->assertIsArray($addConsentRequest->settings);

        $this->assertNotNull($addConsentRequest->optional);
        $this->assertIsBool($addConsentRequest->optional);

        $this->assertNotNull($addConsentRequest->signer_ids);
        $this->assertIsArray($addConsentRequest->signer_ids);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_default_properties_values(): void
    {
        /** @var AddConsentRequest $addConsentRequest */
        $addConsentRequest = new AddConsentRequest();

        $this->assertInstanceOf(AddConsentRequest::class, $addConsentRequest);

        $this->assertNotNull($addConsentRequest->optional);
        $this->assertIsBool($addConsentRequest->optional);
        $this->assertFalse($addConsentRequest->optional);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentRequest::factory
     *
     * @return void
     */
    public function it_throws_exception_on_creation_by_factory(): void
    {
        $this->expectException(StructSaveException::class);

        AddConsentRequest::factory()
                         ->create();
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentRequest::save
     *
     * @return void
     */
    public function it_throws_exception_on_saving(): void
    {
        $this->expectException(StructSaveException::class);

        (new AddConsentRequest())->save();
    }
}
