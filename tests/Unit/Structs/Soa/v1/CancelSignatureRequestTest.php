<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Enums\v1\CancelSignatureReason;
use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Coverzen\Components\YousignClient\Structs\Soa\v1\CancelSignatureRequest;

/**
 * Class CancelSignatureRequestTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Structs\Soa\v1\CancelSignatureRequest
 */
final class CancelSignatureRequestTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_instantiates(): void
    {
        /** @var CancelSignatureRequest $cancelSignatureRequest */
        $cancelSignatureRequest = new CancelSignatureRequest();

        $this->assertInstanceOf(CancelSignatureRequest::class, $cancelSignatureRequest);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\CancelSignatureRequest::factory
     *
     * @return void
     */
    public function it_is_made_by_factory(): void
    {
        /** @var CancelSignatureRequest $cancelSignatureRequest */
        $cancelSignatureRequest = CancelSignatureRequest::factory()
                                              ->make();

        $this->assertInstanceOf(CancelSignatureRequest::class, $cancelSignatureRequest);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_expected_properties(): void
    {
        /** @var CancelSignatureRequest $cancelSignatureRequest */
        $cancelSignatureRequest = CancelSignatureRequest::factory()
                                              ->make();

        $this->assertNotNull($cancelSignatureRequest->reason);
        $this->assertInstanceOf(CancelSignatureReason::class, $cancelSignatureRequest->reason);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_default_properties_values(): void
    {
        /** @var CancelSignatureRequest $cancelSignatureRequest */
        $cancelSignatureRequest = new CancelSignatureRequest();

        $this->assertSame(CancelSignatureReason::contractualization_aborted(), $cancelSignatureRequest->reason);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\CancelSignatureRequest::factory
     *
     * @return void
     */
    public function it_throws_exception_on_creation_by_factory(): void
    {
        $this->expectException(StructSaveException::class);

        CancelSignatureRequest::factory()
                         ->create();
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\CancelSignatureRequest::save
     *
     * @return void
     */
    public function it_throws_exception_on_saving(): void
    {
        $this->expectException(StructSaveException::class);

        (new CancelSignatureRequest())->save();
    }
}
