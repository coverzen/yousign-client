<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\SignerField;

/**
 * Class AddSignerResponseTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerRequest
 */
final class AddSignerResponseTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_instantiates(): void
    {
        $addSignerResponse = new AddSignerResponse();

        $this->assertInstanceOf(AddSignerResponse::class, $addSignerResponse);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerRequest::factory
     *
     * @return void
     */
    public function it_is_made_by_factory(): void
    {
        /** @var AddSignerResponse $addSignerResponse */
        $addSignerResponse = AddSignerResponse::factory()
                                              ->make();

        $this->assertInstanceOf(AddSignerResponse::class, $addSignerResponse);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_expected_properties(): void
    {
        /** @var AddSignerResponse $addSignerResponse */
        $addSignerResponse = AddSignerResponse::factory()
                                              ->make();

        $this->assertIsArray($addSignerResponse->info);
        $this->assertIsString($addSignerResponse->signature_level);
        $this->assertIsString($addSignerResponse->signature_authentication_mode);
        $this->assertIsArray($addSignerResponse->fields);
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

        AddSignerResponse::factory()
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

        (new AddSignerResponse())->save();
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_creates_add_signer_response_with_fields_from_array(): void
    {
        /** @var array<array-key,mixed> $signerResponseData */
        $signerResponseData = AddSignerResponse::factory()
                                               ->make()
                                               ->toArray();

        /** @var AddSignerResponse $addSignerResponse */
        $addSignerResponse = new AddSignerResponse($signerResponseData);

        $this->assertInstanceOf(AddSignerResponse::class, $addSignerResponse);
        $this->assertIsArray($addSignerResponse->fields);

        /** @var SignerField $field */
        foreach ($addSignerResponse->fields as $field) {
            $this->assertInstanceOf(SignerField::class, $field);
        }
    }
}
