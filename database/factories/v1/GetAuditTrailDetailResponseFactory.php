<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Coverzen\Components\YousignClient\Structs\Soa\v1\GetAuditTrailDetailResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class GetAuditTrailDetailResponseFactory.
 *
 * @template TModel of GetAuditTrailDetailResponse
 * @extends Factory<TModel>
 */
final class GetAuditTrailDetailResponseFactory extends AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = GetAuditTrailDetailResponse::class;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'version' => $this->faker->randomNumber(9),
            'signature_request' => [
                'id' => $this->faker->uuid(),
                'name' => $this->faker->sentence(),
                'sent_at' => $this->faker->dateTimeThisYear->format('Y-m-d\TH:i:sP'),
                'expired_at' => $this->faker->dateTimeThisYear->format('Y-m-d\TH:i:sP'),
            ],
            'organization' => [
                'id' => $this->faker->uuid(),
                'name' => $this->faker->sentence(),
            ],
            'sender' => [
                'type' => $this->faker->sentence(),
                'id' => $this->faker->uuid(),
                'ip_address' => $this->faker->sentence(),
            ],
            'signer' => [
                'id' => $this->faker->uuid(),
                'first_name' => $this->faker->firstName(),
                'last_name' => $this->faker->lastName(),
                'email_address' => $this->faker->email(),
                'phone_number' => $this->faker->phoneNumber(),
                'ip_address' => $this->faker->sentence(),
                'consent_given_at' => $this->faker->dateTimeThisYear->format('Y-m-d\TH:i:sP'),
                'signature_process_completed_at' => $this->faker->dateTimeThisYear->format('Y-m-d\TH:i:sP'),
                'context' => $this->faker->sentence(),
            ],
            'authentication' => [
                'mode' => $this->faker->sentence(),
                'code' => $this->faker->sentence(),
                'validated_at' => $this->faker->dateTimeThisYear->format('Y-m-d\TH:i:sP'),
            ],
            'documents' => [
                'id' => $this->faker->uuid(),
                'name' => $this->faker->sentence(),
                'mime_type' => $this->faker->sentence(),
                'initial_mime_type' => $this->faker->sentence(),
                'initial_name' => $this->faker->sentence(),
                'initial_storage_id' => $this->faker->uuid(),
                'initial_hash' => $this->faker->sentence(),
                'sentence' => [],
            ],
            'signer_consents' => [
                [
                    'type' => $this->faker->sentence(),
                    'required' => $this->faker->sentence(),
                    'text' => $this->faker->sentence(),
                    'state' => $this->faker->sentence(),
                ],
                [
                    'type' => $this->faker->sentence(),
                    'required' => $this->faker->sentence(),
                    'text' => $this->faker->sentence(),
                    'state' => $this->faker->sentence(),
                ],
            ],
        ];
    }
}
