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
        $this->assertIsArray($initiateSignatureRequest->email_notification);
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

    /**
     * @test
     *
     * @return void
     */
    public function it_has_payload_accessor(): void
    {
        /** @var InitiateSignatureRequest $initiateSignatureRequest */
        $initiateSignatureRequest = InitiateSignatureRequest::factory()
                                                            ->make();

        $this->assertNotNull($initiateSignatureRequest->payload);
        $this->assertIsArray($initiateSignatureRequest->payload);

        $this->assertArrayNotHasKey('payload', $initiateSignatureRequest->payload);

        $this->assertArrayHasKey('name', $initiateSignatureRequest->payload);
        $this->assertArrayHasKey('delivery_mode', $initiateSignatureRequest->payload);
        $this->assertArrayHasKey('ordered_signers', $initiateSignatureRequest->payload);
        $this->assertArrayHasKey('timezone', $initiateSignatureRequest->payload);
        $this->assertArrayHasKey('email_notification', $initiateSignatureRequest->payload);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_removes_null_values_in_payload_accessor(): void
    {
        /** @var InitiateSignatureRequest $initiateSignatureRequest */
        $initiateSignatureRequest = InitiateSignatureRequest::factory()
                                                            ->make(
                                                                [
                                                                    'name' => null,
                                                                    'ordered_signers' => null,
                                                                    'timezone' => null,
                                                                    'email_notification' => null,
                                                                ]
                                                            );

        $this->assertNotNull($initiateSignatureRequest->payload);
        $this->assertIsArray($initiateSignatureRequest->payload);

        $this->assertArrayNotHasKey('payload', $initiateSignatureRequest->payload);

        $this->assertArrayNotHasKey('name', $initiateSignatureRequest->payload);
        $this->assertArrayNotHasKey('ordered_signers', $initiateSignatureRequest->payload);
        $this->assertArrayNotHasKey('timezone', $initiateSignatureRequest->payload);
        $this->assertArrayNotHasKey('email_notification', $initiateSignatureRequest->payload);
    }
}
