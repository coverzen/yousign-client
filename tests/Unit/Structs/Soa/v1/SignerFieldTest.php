<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Tests\Unit\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Exceptions\Structs\v1\StructSaveException;
use Coverzen\Components\YousignClient\Structs\Soa\v1\SignerField;

/**
 * Class SignerFieldTest.
 *
 * @coversDefaultClass \Coverzen\Components\YousignClient\Structs\Soa\v1\SignerField
 */
final class SignerFieldTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function it_instantiates(): void
    {
        /** @var SignerField $signerField */
        $signerField = new SignerField();

        $this->assertInstanceOf(SignerField::class, $signerField);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_is_made_by_factory(): void
    {
        /** @var SignerField $signerField */
        $signerField = SignerField::factory()
                                  ->make();

        $this->assertInstanceOf(SignerField::class, $signerField);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_has_correct_properties_type(): void
    {
        /** @var SignerField $signerField */
        $signerField = SignerField::factory()
                                  ->make();

        $this->assertIsString($signerField->document_id);
        $this->assertIsString($signerField->type);
        $this->assertIsInt($signerField->height);
        $this->assertIsInt($signerField->width);
        $this->assertIsInt($signerField->page);
        $this->assertIsInt($signerField->x);
        $this->assertIsInt($signerField->y);
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_throws_exception_on_creation_by_factory(): void
    {
        $this->expectException(StructSaveException::class);

        SignerField::factory()
                   ->create();
    }

    /**
     * @test
     *
     * @return void
     */
    public function it_throws_exception_on_saving(): void
    {
        $this->expectException(StructSaveException::class);

        (new SignerField())->save();
    }
}
