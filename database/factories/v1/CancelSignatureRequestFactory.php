<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Database\Factories\v1;

use Coverzen\Components\YousignClient\Enums\v1\CancelSignatureReason;
use Coverzen\Components\YousignClient\Structs\Soa\v1\CancelSignatureRequest;

/**
 * Class CancelSignatureRequestFactory.
 *
 * @template TModel of CancelSignatureRequest
 * @extends AbstractFactory<TModel>
 */
final class CancelSignatureRequestFactory extends AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = CancelSignatureRequest::class;

    /**
     * {@inheritdoc}
     */
    public function definition(): array
    {
        return [
            'reason' => $this->faker->randomEnumValue(CancelSignatureReason::class),
            'custom_note' => $this->faker->sentence(),
        ];
    }
}
