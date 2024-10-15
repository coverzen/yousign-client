<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Coverzen\Components\YousignClient\Structs\Soa\v1\SignerField;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class SignerFieldFactory.
 *
 * @template TModel of SignerField
 * @extends Factory<TModel>
 */
final class SignerFieldFactory extends AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = SignerField::class;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'document_id' => $this->faker->sentence,
            'type' => $this->faker->sentence,
            'height' => $this->faker->numberBetween(),
            'width' => $this->faker->numberBetween(),
            'page' => $this->faker->numberBetween(),
            'x' => $this->faker->numberBetween(),
            'y' => $this->faker->numberBetween(),
        ];
    }
}
