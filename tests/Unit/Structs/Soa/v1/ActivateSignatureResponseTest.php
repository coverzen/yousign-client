<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Coverzen\Components\YousignClient\Structs\Soa\v1\ActivateSignatureResponse;

/**
 * Class ActivateSignatureResponseTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Structs\Soa\v1\ActivateSignatureResponse
 */
final class ActivateSignatureResponseTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_instantiates(): void
    {
        $activateSignatureResponse = new ActivateSignatureResponse();

        $this->assertInstanceOf(ActivateSignatureResponse::class, $activateSignatureResponse);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\ActivateSignatureResponse::factory
     *
     * @return void
     */
    public function it_is_made_by_factory(): void
    {
        /** @var ActivateSignatureResponse $activateSignatureResponse */
        $activateSignatureResponse = ActivateSignatureResponse::factory()
                                              ->make();

        $this->assertInstanceOf(ActivateSignatureResponse::class, $activateSignatureResponse);
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\ActivateSignatureResponse::factory
     *
     * @return void
     */
    public function it_throws_exception_on_creation_by_factory(): void
    {
        $this->expectException(StructSaveException::class);

        ActivateSignatureResponse::factory()
                         ->create();
    }

    /**
     * @test
     * @covers \Coverzen\Components\YousignClient\Structs\Soa\v1\ActivateSignatureResponse::save
     *
     * @return void
     */
    public function it_throws_exception_on_saving(): void
    {
        $this->expectException(StructSaveException::class);

        (new ActivateSignatureResponse())->save();
    }
}
