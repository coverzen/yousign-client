<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Coverzen\Components\YousignClient\Enums\v1\ActivateSignatureResponseStatus;
use Coverzen\Components\YousignClient\Enums\v1\DeliveryMode;
use Coverzen\Components\YousignClient\Structs\Soa\v1\ActivateSignatureResponse;

/**
 * Class ActivateSignatureResponseFactory.
 *
 * @template TModel of ActivateSignatureResponse
 * @extends AbstractFactory<TModel>
 */
final class ActivateSignatureResponseFactory extends AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = ActivateSignatureResponse::class;

    /** @var int */
    public const MIN_DAYS = 0;

    /** @var int */
    public const MAX_DAYS = 30;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'status' => $this->faker->randomEnumValue(ActivateSignatureResponseStatus::class),
            'name' => $this->faker->name(),
            'delivery_mode' => $this->faker->randomEnumValue(DeliveryMode::class),
            'created_at' => $this->faker->dateTime(),
            'ordered_signers' => $this->faker->boolean(),
            'reminder_settings' => [
                'interval_in_days' => $this->faker->numberBetween(self::MIN_DAYS, self::MAX_DAYS),
                'max_occurrences' => $this->faker->numberBetween(self::MIN_DAYS, self::MAX_DAYS),
            ],
            'timezone' => 'Europe/Paris',
            'expiration_date' => $this->faker->dateTime(),
            'signers' => [],
            'approvers' => [],
            'documents' => [],
            'external_id' => $this->faker->uuid(),
            'branding_id' => $this->faker->uuid(),
            'custom_experience_id' => $this->faker->uuid(),
            'audit_trail_locale' => 'it',
        ];
    }
}
