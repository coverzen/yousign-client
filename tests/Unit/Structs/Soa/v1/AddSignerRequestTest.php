<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Structs\Soa\v1;

use Closure;
use Coverzen\Components\YousignClient\Enums\v1\SignatureAuthenticationMode;
use Coverzen\Components\YousignClient\Enums\v1\SignatureLevel;
use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * Class AddSignerRequestTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerRequest
 */
final class AddSignerRequestTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_instantiates(): void
    {
        $addSignerRequest = new AddSignerRequest();

        $this->assertInstanceOf(AddSignerRequest::class, $addSignerRequest);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerRequest::factory
     *
     * @return void
     */
    public function it_is_made_by_factory(): void
    {
        /** @var AddSignerRequest $addSignerRequest */
        $addSignerRequest = AddSignerRequest::factory()
                                            ->make();

        $this->assertInstanceOf(AddSignerRequest::class, $addSignerRequest);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_expected_properties(): void
    {
        /** @var AddSignerRequest $addSignerRequest */
        $addSignerRequest = AddSignerRequest::factory()
                                            ->make();

        $this->assertIsArray($addSignerRequest->info);
        $this->assertInstanceOf(SignatureLevel::class, $addSignerRequest->signature_level);
        $this->assertInstanceOf(SignatureAuthenticationMode::class, $addSignerRequest->signature_authentication_mode);
        $this->assertIsArray($addSignerRequest->fields);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentRequest::factory
     *
     * @return void
     */
    public function it_throws_exception_on_creation_by_factory(): void
    {
        $this->expectException(StructSaveException::class);

        AddSignerRequest::factory()
                        ->create();
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentRequest::save
     *
     * @return void
     */
    public function it_throws_exception_on_saving(): void
    {
        $this->expectException(StructSaveException::class);

        (new AddSignerRequest())->save();
    }

    #[Test]
    public function it_passes_validation_with_a_complete_signer(): void
    {
        /** @var AddSignerRequest $addSignerRequest */
        $addSignerRequest = AddSignerRequest::factory()
                                            ->make();

        $this->expectNotToPerformAssertions();

        $addSignerRequest->validate();
    }

    /**
     * @return array<string,array{Closure,string}>
     */
    public static function invalidInfoProvider(): array
    {
        return [
            'missing first_name' => [static fn (array $info): array => Arr::except($info, ['first_name']), 'first name'],
            'missing last_name' => [static fn (array $info): array => Arr::except($info, ['last_name']), 'last name'],
            'missing email' => [static fn (array $info): array => Arr::except($info, ['email']), 'email'],
            'missing locale' => [static fn (array $info): array => Arr::except($info, ['locale']), 'locale'],
            'unsupported locale' => [static fn (array $info): array => [...$info, 'locale' => 'pt'], 'locale'],
        ];
    }

    #[Test]
    #[DataProvider('invalidInfoProvider')]
    public function it_fails_validation_when_the_signer_info_is_invalid(Closure $mutateInfo, string $messageFragment): void
    {
        /** @var AddSignerRequest $addSignerRequest */
        $addSignerRequest = AddSignerRequest::factory()
                                            ->make();

        /** @phpstan-ignore assign.propertyType */
        $addSignerRequest->info = $mutateInfo($addSignerRequest->info);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage($messageFragment);

        $addSignerRequest->validate();
    }
}
