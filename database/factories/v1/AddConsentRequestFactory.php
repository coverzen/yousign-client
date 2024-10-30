<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentRequest;

/**
 * Class AddConsentRequestFactory.
 *
 * @template TModel of AddConsentRequest
 * @extends AbstractFactory<TModel>
 */
final class AddConsentRequestFactory extends AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = AddConsentRequest::class;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
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
