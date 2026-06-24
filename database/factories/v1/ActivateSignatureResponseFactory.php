<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Coverzen\Components\YousignClient\Enums\v1\ActivateSignatureResponseStatus;
use Coverzen\Components\YousignClient\Enums\v1\DeliveryMode;
use Coverzen\Components\YousignClient\Structs\Soa\v1\ActivateSignatureResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class ActivateSignatureResponseFactory.
 *
 * @extends Factory<ActivateSignatureResponse>
 */
final class ActivateSignatureResponseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ActivateSignatureResponse>
     */
    protected $model = ActivateSignatureResponse::class;

    /** @var int */
    public const MIN_DAYS = 0;

    /** @var int */
    public const MAX_DAYS = 30;

    /** @var string */
    public const SIGNER_ID = 'signer-1-id';

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'status' => $this->faker->randomElement(ActivateSignatureResponseStatus::class),
            'name' => $this->faker->name(),
            'delivery_mode' => $this->faker->randomElement(DeliveryMode::class),
            'created_at' => $this->faker->dateTime(),
            'ordered_signers' => $this->faker->boolean(),
            'reminder_settings' => [
                'interval_in_days' => $this->faker->numberBetween(self::MIN_DAYS, self::MAX_DAYS),
                'max_occurrences' => $this->faker->numberBetween(self::MIN_DAYS, self::MAX_DAYS),
            ],
            'timezone' => 'Europe/Paris',
            'expiration_date' => $this->faker->dateTime(),
            'signers' => [
                [
                    'id' => self::SIGNER_ID,
                    'status' => 'initiated',
                    'delivery_mode' => null,
                    'signature_link' => $this->faker->url(),
                    'signature_link_expiration_date' => $this->faker->dateTime(),
                ],
            ],
            'approvers' => [],
            'documents' => [],
            'external_id' => $this->faker->uuid(),
            'branding_id' => $this->faker->uuid(),
            'custom_experience_id' => $this->faker->uuid(),
            'audit_trail_locale' => 'it',
        ];
    }
}
