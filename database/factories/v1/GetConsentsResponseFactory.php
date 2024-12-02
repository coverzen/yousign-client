<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\GetConsentsResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class GetConsentsResponseFactory.
 *
 * @template TModel of GetConsentsResponse
 * @extends Factory<TModel>
 */
final class GetConsentsResponseFactory extends AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = GetConsentsResponse::class;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'data' => [
                'id' => $this->faker->uuid(),
                'type' => AddConsentRequest::DEFAULT_TYPE,
                'settings' => [
                    'text' => $this->faker->sentence(),
                ],
                'optional' => $this->faker->boolean(),
                'signer_ids' => [
                    $this->faker->uuid(),
                ],
            ],
        ];
    }
}
