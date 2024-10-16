<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Coverzen\Components\YousignClient\Enums\v1\SignatureAuthenticationMode;
use Coverzen\Components\YousignClient\Enums\v1\SignatureLevel;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\SignerField;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * Class AddSignerResponseFactory.
 *
 * @template TModel of AddSignerResponse
 * @extends Factory<TModel>
 */
final class AddSignerResponseFactory extends AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = AddSignerResponse::class;

    /**
     * {@inheritdoc}
     */
    public function configure(): static
    {
        return $this->afterMaking(
            /**
             * Necessary because association of model without persistence in factory will break
             * returning null properties.
             *
             * @param AddSignerResponse $addSignerResponse
             *
             * @retrun void
             * @see Factory::expandAttributes() 485
             *
             * @throws RandomException
             */
            static function (AddSignerResponse $addSignerResponse): void {
                $addSignerResponse->fields = SignerField::factory()
                                                        ->count(random_int(2, 5))
                                                        ->make()->all();
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'info' => [],
            'status' => $this->faker->sentence,
            'signature_level' => $this->faker->randomEnumValue(SignatureLevel::class),
            'signature_authentication_mode' => $this->faker->randomEnumValue(SignatureAuthenticationMode::class),
            'signature_link' => $this->faker->url(),
            'signature_link_expiration_date' => $this->faker->dateTime,
            'signature_image_preview' => $this->faker->sentence,
            'redirect_urls' => [],
            'custom_text' => [],
            'delivery_mode' => 'email',
            'identification_attestation_id' => null,
            'sms_notification' => [],
        ];
    }
}
