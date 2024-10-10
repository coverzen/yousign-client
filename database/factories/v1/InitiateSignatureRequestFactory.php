<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class InitiateSignatureRequestFactory.
 *
 * @template TModel of InitiateSignatureRequest
 * @extends Factory<TModel>
 */
final class InitiateSignatureRequestFactory extends AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = InitiateSignatureRequest::class;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'delivery_mode' => 'none',
            'ordered_signers' => $this->faker->boolean(),
            'timezone' => $this->faker->timezone(),
        ];
    }
}