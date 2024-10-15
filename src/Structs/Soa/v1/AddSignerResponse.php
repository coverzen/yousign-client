<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\AddSignerRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class AddSignerRequest.
 *
 * @property array $info
 * @property string $signature_level
 * @property string $signature_authentication_mode
 * @property SignerField $fields
 */
final class AddSignerResponse extends Struct
{
    use HasFactory;

    /** {@inheritdoc} */
    public $timestamps = false;

    /** {@inheritdoc} */
    protected $fillable = [
    ];

    /** @var array<array-key,mixed> */
    protected $attributes = [
    ];

    /** {@inheritdoc} */
    protected $casts = [
    ];
    //
    //    /**
    //     * Set the proper factory for model.
    //     *
    //     * @return AddSignerRequestFactory<self>
    //     */
    //    protected static function newFactory(): AddSignerRequestFactory
    //    {
    //        return AddSignerRequestFactory::new();
    //    }
}
