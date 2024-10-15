<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Coverzen\Components\YousignClient\Enums\v1\SignatureAuthenticationMode;
use Coverzen\Components\YousignClient\Enums\v1\SignatureLevel;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\SignerField;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class AddSignerRequestFactory.
 *
 * @template TModel of AddSignerRequest
 * @extends Factory<TModel>
 */
final class AddSignerRequestFactory extends AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = AddSignerRequest::class;

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
             * @see Factory::expandAttributes() 485
             *
             * @param AddSignerRequest $addSignerRequest
             *
             * @retrun void
             */
            static function (AddSignerRequest $addSignerRequest): void {
                $addSignerRequest->fields = SignerField::factory(random_int(2, 5))
                                                       ->make();
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'info' => [],
            'signature_level' => $this->faker->randomEnumValue(SignatureLevel::class),
            'signature_authentication_mode' => $this->faker->randomEnumValue(SignatureAuthenticationMode::class),
        ];
    }
}
