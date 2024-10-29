<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Enums\v1\DeliveryMode;
use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest;
use Coverzen\Components\YousignClient\YousignClientServiceProvider;
use Illuminate\Support\Facades\Config;

/**
 * Class InitiateSignatureRequestTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest
 */
final class InitiateSignatureRequestTest extends TestCase
{
    /** @var string */
    private const FAKE_CUSTOM_EXPERIENCE_ID = 'fake-custom-experience-id';

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
     * @dataProvider customerExperienceProvider
     *
     * @param bool $customExperience
     *
     * @return void
     */
    public function it_has_expected_properties(bool $customExperience): void
    {
        if ($customExperience) {
            Config::set(YousignClientServiceProvider::CONFIG_KEY . '.custom_experience_id', self::FAKE_CUSTOM_EXPERIENCE_ID);
        }

        /** @var InitiateSignatureRequest $initiateSignatureRequest */
        $initiateSignatureRequest = InitiateSignatureRequest::factory()
                                                            ->make();
        $this->assertNotNull($initiateSignatureRequest->name);
        $this->assertIsString($initiateSignatureRequest->name);

        $this->assertNotNull($initiateSignatureRequest->delivery_mode);
        $this->assertInstanceOf(DeliveryMode::class, $initiateSignatureRequest->delivery_mode);

        $this->assertNotNull($initiateSignatureRequest->ordered_signers);
        $this->assertIsBool($initiateSignatureRequest->ordered_signers);

        $this->assertNotNull($initiateSignatureRequest->timezone);
        $this->assertIsString($initiateSignatureRequest->timezone);

        $this->assertIsArray($initiateSignatureRequest->email_notification);

        if ($customExperience) {
            $this->assertNotNull($initiateSignatureRequest->custom_experience_id);
        } else {
            $this->assertNull($initiateSignatureRequest->custom_experience_id);
        }
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_default_properties_values(): void
    {
        Config::set(YousignClientServiceProvider::CONFIG_KEY . '.custom_experience_id', self::FAKE_CUSTOM_EXPERIENCE_ID);

        /** @var InitiateSignatureRequest $initiateSignatureRequest */
        $initiateSignatureRequest = new InitiateSignatureRequest();

        $this->assertSame(DeliveryMode::none(), $initiateSignatureRequest->delivery_mode);
        $this->assertSame(Config::get(YousignClientServiceProvider::CONFIG_KEY . '.custom_experience_id'), $initiateSignatureRequest->custom_experience_id);
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
     * @dataProvider customerExperienceProvider
     *
     * @param bool $customExperience
     *
     * @return void
     */
    public function it_has_payload_accessor(bool $customExperience): void
    {
        if ($customExperience) {
            Config::set(YousignClientServiceProvider::CONFIG_KEY . '.custom_experience_id', self::FAKE_CUSTOM_EXPERIENCE_ID);
        }

        /** @var InitiateSignatureRequest $initiateSignatureRequest */
        $initiateSignatureRequest = InitiateSignatureRequest::factory()
                                                            ->make();

        $this->assertNotNull($initiateSignatureRequest->payload);
        $this->assertIsArray($initiateSignatureRequest->payload);

        $this->assertArrayNotHasKey('payload', $initiateSignatureRequest->payload);

        $this->assertArrayHasKey('name', $initiateSignatureRequest->payload);

        $this->assertArrayHasKey('delivery_mode', $initiateSignatureRequest->payload);
        $this->assertIsString($initiateSignatureRequest->payload['delivery_mode']);

        $this->assertArrayHasKey('ordered_signers', $initiateSignatureRequest->payload);
        $this->assertArrayHasKey('timezone', $initiateSignatureRequest->payload);
        $this->assertArrayHasKey('email_notification', $initiateSignatureRequest->payload);

        if ($customExperience) {
            $this->assertArrayHasKey('custom_experience_id', $initiateSignatureRequest->payload);
        } else {
            $this->assertArrayNotHasKey('custom_experience_id', $initiateSignatureRequest->payload);
        }
    }

    /**
     * Provides a set of.
     *
     * @return array<string,array<string,mixed>>
     */
    public static function customerExperienceProvider(): array
    {
        return [
            'custom experience' => [
                'customExperience' => true,
            ],
            'no custom experience' => [
                'customExperience' => false,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider customerExperienceProvider
     *
     * @param bool $customExperience
     *
     * @return void
     */
    public function it_removes_null_values_in_payload_accessor(bool $customExperience): void
    {
        if ($customExperience) {
            Config::set(YousignClientServiceProvider::CONFIG_KEY . '.custom_experience_id', self::FAKE_CUSTOM_EXPERIENCE_ID);
        }

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

        if ($customExperience) {
            $this->assertArrayHasKey('custom_experience_id', $initiateSignatureRequest->payload);
        } else {
            $this->assertArrayNotHasKey('custom_experience_id', $initiateSignatureRequest->payload);
        }
    }
}
