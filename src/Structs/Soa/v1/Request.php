<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Enum\Laravel\Enum;
use function collect;

/**
 * Class Request.
 *
 * @property-read array $payload
 */
abstract class Request extends Struct
{
    /**
     * Define accessor for `payload` attribute.
     * It basically removes all null properties and cast attributes.
     * It also return plain value for attributes that are Enums.
     *
     * @return Attribute<array<array-key,mixed>,null>
     */
    protected function payload(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                return collect($this->attributesToArray())->filter(static fn ($value): bool => $value !== null)
                                                          ->map(static fn ($value): mixed => $value instanceof Enum ? $value->value : $value)
                                                          ->toArray();
            }
        );
    }
}
