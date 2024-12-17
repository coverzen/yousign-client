<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Coverzen\Components\YousignClient\Structs\Soa\v1\SignatureRequestResponse;

/**
 * Class SignatureRequestResponseFactory.
 *
 * @template TModel of SignatureRequestResponse
 * @extends AbstractFactory<TModel>
 */
final class SignatureRequestResponseFactory extends AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = SignatureRequestResponse::class;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'source' => 'public_api',
            'status' => 'draft',
            'name' => $this->faker->sentence(),
            'created_at' => $this->faker->dateTimeThisYear->format('Y-m-d\TH:i:sP'),
            'email_custom_note' => null,
            'ordered_signers' => false,
            'timezone' => $this->faker->timezone(),
            'reminder_settings' => null,
            'expiration_date' => $this->faker->dateTimeThisYear->format('Y-m-d\TH:i:sP'),
            'delivery_mode' => 'none',
            'documents' => [],
            'signers' => [],
            'external_id' => null,
            'branding_id' => null,
            'custom_experience_id' => null,
            'sender' => null,
            'workspace_id' => $this->faker->uuid,
            'audit_trail_locale' => 'en',
            'signers_allowed_to_decline' => false,
            'bulk_send_batch_id' => null,
            'email_notification' => [
                'sender' => [
                    'type' => 'organization',
                    'custom_name' => null,
                ],
                'custom_note' => null,
            ],
        ];
    }
}
