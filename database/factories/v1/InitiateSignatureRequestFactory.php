<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Coverzen\Components\YousignClient\Enums\v1\DeliveryMode;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class InitiateSignatureRequestFactory.
 *
 * @extends Factory<InitiateSignatureRequest>
 */
final class InitiateSignatureRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<InitiateSignatureRequest>
     */
    protected $model = InitiateSignatureRequest::class;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'delivery_mode' => $this->faker->randomElement(DeliveryMode::class),
            'ordered_signers' => $this->faker->boolean(),
            'timezone' => $this->faker->timezone(),
            'email_notification' => [
                'sender' => [
                    'type' => 'organization',
                ],
                'custom_note' => $this->faker->sentence(),
            ],
            'custom_experience_id' => $this->faker->uuid(),
        ];
    }
}
