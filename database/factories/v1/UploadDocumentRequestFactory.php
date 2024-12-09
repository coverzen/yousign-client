<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Coverzen\Components\YousignClient\Enums\v1\DocumentNature;
use Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentRequest;

/**
 * Class UploadDocumentRequestFactory.
 *
 * @template TModel of UploadDocumentRequest
 * @extends AbstractFactory<TModel>
 */
final class UploadDocumentRequestFactory extends AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = UploadDocumentRequest::class;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'file_content' => $this->faker->text(),
            'file_name' => "{$this->faker->word()}.{$this->faker->fileExtension()}",
            'nature' => $this->faker->randomEnumValue(DocumentNature::class),
        ];
    }
}
