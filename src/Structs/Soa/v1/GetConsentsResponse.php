<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\GetConsentsResponseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class GetConsentsResponse.
 *
 * @property array<array-key,array> $data
 */
final class GetConsentsResponse extends Struct
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $fillable = [
        'data',
    ];

    /** {@inheritdoc} */
    protected $attributes = [
        'data' => [],
    ];

    /**
     * Set the proper factory for the model.
     *
     * @return GetConsentsResponseFactory<self>
     */
    protected static function newFactory(): GetConsentsResponseFactory
    {
        return GetConsentsResponseFactory::new();
    }
}
