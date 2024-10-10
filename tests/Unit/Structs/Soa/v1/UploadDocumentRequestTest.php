<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Enums\v1\DocumentNature;
use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentRequest;

/**
 * Class UploadDocumentRequestTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentRequest
 */
final class UploadDocumentRequestTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_instantiates(): void
    {
        /** @var UploadDocumentRequest $uploadDocumentRequest */
        $uploadDocumentRequest = new UploadDocumentRequest();

        $this->assertInstanceOf(UploadDocumentRequest::class, $uploadDocumentRequest);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentRequest::factory
     *
     * @return void
     */
    public function it_is_made_by_factory(): void
    {
        /** @var UploadDocumentRequest $uploadDocumentRequest */
        $uploadDocumentRequest = UploadDocumentRequest::factory()
                                                      ->make();

        $this->assertInstanceOf(UploadDocumentRequest::class, $uploadDocumentRequest);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_expected_properties(): void
    {
        /** @var UploadDocumentRequest $uploadDocumentRequest */
        $uploadDocumentRequest = UploadDocumentRequest::factory()
                                                      ->make();

        $this->assertIsString($uploadDocumentRequest->file_content);
        $this->assertIsString($uploadDocumentRequest->file_name);
        $this->assertInstanceOf(DocumentNature::class, $uploadDocumentRequest->nature);
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

        UploadDocumentRequest::factory()
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

        (new UploadDocumentRequest())->save();
    }
}
