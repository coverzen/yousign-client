<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Coverzen\Components\YousignClient\Enums\v1\CancelSignatureReason;
use Coverzen\Components\YousignClient\Structs\Soa\v1\CancelSignatureRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class CancelSignatureRequestFactory.
 *
 * @extends Factory<CancelSignatureRequest>
 */
final class CancelSignatureRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<CancelSignatureRequest>
     */
    protected $model = CancelSignatureRequest::class;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'reason' => $this->faker->randomElement(CancelSignatureReason::class),
            'custom_note' => $this->faker->sentence(),
        ];
    }
}
