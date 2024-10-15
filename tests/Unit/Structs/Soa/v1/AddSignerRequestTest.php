<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\SignerField;

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
        $this->assertIsString($addSignerRequest->signature_level);
        $this->assertIsString($addSignerRequest->signature_authentication_mode);
        $this->assertInstanceOf(SignerField::class, $addSignerRequest->fields);
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
}
