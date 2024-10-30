<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentResponse;

/**
 * Class AddConsentResponseFactory.
 *
 * @template TModel of AddConsentResponse
 * @extends AbstractFactory<TModel>
 */
final class AddConsentResponseFactory extends AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = AddConsentResponse::class;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'type' => AddConsentRequest::DEFAULT_TYPE,
            'settings' => [
                'text' => $this->faker->sentence(),
            ],
            'optional' => $this->faker->boolean(),
            'signer_ids' => [
                $this->faker->uuid(),
            ],
        ];
    }
}
