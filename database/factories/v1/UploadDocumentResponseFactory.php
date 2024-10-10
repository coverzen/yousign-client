<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class UploadDocumentResponseFactory.
 *
 * @template TModel of UploadDocumentResponse
 * @extends Factory<TModel>
 */
final class UploadDocumentResponseFactory extends AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = UploadDocumentResponse::class;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'filename' => $this->faker->name(),
            'nature' => $this->faker->name(),
            'content_type' => $this->faker->name(),
            'sha256' => $this->faker->sha256(),
            'is_protected' => $this->faker->boolean(),
            'is_signed' => $this->faker->boolean(),
            'created_at' => $this->faker->dateTime(),
            'total_pages' => $this->faker->numberBetween(1, 10),
            'is_locked' => $this->faker->boolean(),
            'initials' => $this->faker->name(),
            'total_anchors' => $this->faker->numberBetween(1, 10),
        ];
    }
}
