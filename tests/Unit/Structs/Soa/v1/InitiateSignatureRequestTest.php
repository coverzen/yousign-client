<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Enums\v1\DeliveryMode;
use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest;

/**
 * Class InitiateSignatureRequestTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest
 */
final class InitiateSignatureRequestTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_instantiates(): void
    {
        /** @var InitiateSignatureRequest $initiateSignatureRequest */
        $initiateSignatureRequest = new InitiateSignatureRequest();

        $this->assertInstanceOf(InitiateSignatureRequest::class, $initiateSignatureRequest);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest::factory
     *
     * @return void
     */
    public function it_is_made_by_factory(): void
    {
        /** @var InitiateSignatureRequest $initiateSignatureRequest */
        $initiateSignatureRequest = InitiateSignatureRequest::factory()
                                                            ->make();

        $this->assertInstanceOf(InitiateSignatureRequest::class, $initiateSignatureRequest);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_expected_properties(): void
    {
        /** @var InitiateSignatureRequest $initiateSignatureRequest */
        $initiateSignatureRequest = InitiateSignatureRequest::factory()
                                                            ->make();

        $this->assertIsString($initiateSignatureRequest->name);
        $this->assertInstanceOf(DeliveryMode::class, $initiateSignatureRequest->delivery_mode);
        $this->assertIsBool($initiateSignatureRequest->ordered_signers);
        $this->assertIsString($initiateSignatureRequest->timezone);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_default_properties_values(): void
    {
        /** @var InitiateSignatureRequest $initiateSignatureRequest */
        $initiateSignatureRequest = new InitiateSignatureRequest();

        $this->assertSame(DeliveryMode::none(), $initiateSignatureRequest->delivery_mode);
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

        InitiateSignatureRequest::factory()
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

        (new InitiateSignatureRequest())->save();
    }
}
