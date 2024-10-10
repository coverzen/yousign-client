<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentResponse;
use Illuminate\Support\Carbon;

/**
 * Class UploadDocumentResponseTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentResponse
 */
final class UploadDocumentResponseTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_instantiates(): void
    {
        /** @var UploadDocumentResponse $uploadDocumentResponse */
        $uploadDocumentResponse = new UploadDocumentResponse();

        $this->assertInstanceOf(UploadDocumentResponse::class, $uploadDocumentResponse);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentResponse::factory
     *
     * @return void
     */
    public function it_is_made_by_factory(): void
    {
        /** @var UploadDocumentResponse $uploadDocumentResponse */
        $uploadDocumentResponse = UploadDocumentResponse::factory()
                                                        ->make();

        $this->assertInstanceOf(UploadDocumentResponse::class, $uploadDocumentResponse);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_expected_properties(): void
    {
        /** @var UploadDocumentResponse $uploadDocumentResponse */
        $uploadDocumentResponse = UploadDocumentResponse::factory()
                                                        ->make();

        $this->assertIsString($uploadDocumentResponse->id);
        $this->assertIsString($uploadDocumentResponse->filename);
        $this->assertIsString($uploadDocumentResponse->nature);
        $this->assertIsString($uploadDocumentResponse->content_type);
        $this->assertIsString($uploadDocumentResponse->sha256);
        $this->assertIsBool($uploadDocumentResponse->is_protected);
        $this->assertIsBool($uploadDocumentResponse->is_signed);
        $this->assertInstanceOf(Carbon::class, $uploadDocumentResponse->created_at);
        $this->assertIsInt($uploadDocumentResponse->total_pages);
        $this->assertIsBool($uploadDocumentResponse->is_locked);
        $this->assertIsString($uploadDocumentResponse->initials);
        $this->assertIsInt($uploadDocumentResponse->total_anchors);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_correct_defaults(): void
    {
        /** @var UploadDocumentResponse $uploadDocumentResponse */
        $uploadDocumentResponse = new UploadDocumentResponse();

        $this->assertIsBool($uploadDocumentResponse->is_protected);
        $this->assertFalse($uploadDocumentResponse->is_protected);

        $this->assertIsBool($uploadDocumentResponse->is_signed);
        $this->assertFalse($uploadDocumentResponse->is_signed);

        $this->assertIsBool($uploadDocumentResponse->is_locked);
        $this->assertFalse($uploadDocumentResponse->is_locked);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_correct_casts(): void
    {
        /** @var UploadDocumentResponse $uploadDocumentResponse */
        $uploadDocumentResponse = new UploadDocumentResponse(
            [
                'is_protected' => 0,
                'is_signed' => 0,
                'is_locked' => 0,
                'created_at' => Carbon::now()->toDateTimeString(),
            ]
        );

        $this->assertIsBool($uploadDocumentResponse->is_protected);
        $this->assertIsBool($uploadDocumentResponse->is_signed);
        $this->assertIsBool($uploadDocumentResponse->is_locked);
        $this->assertInstanceOf(Carbon::class, $uploadDocumentResponse->created_at);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentResponse::factory
     *
     * @return void
     */
    public function it_throws_exception_on_creation_by_factory(): void
    {
        $this->expectException(StructSaveException::class);

        UploadDocumentResponse::factory()
                              ->create();
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentResponse::save
     *
     * @return void
     */
    public function it_throws_exception_on_saving(): void
    {
        $this->expectException(StructSaveException::class);

        (new UploadDocumentResponse())->save();
    }
}
