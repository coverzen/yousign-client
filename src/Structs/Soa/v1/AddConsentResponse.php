<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\AddConsentResponseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class AddConsentResponse.
 *
 * @property string|null $id
 * @property string|null $type
 * @property array|null $settings
 * @property bool $optional
 * @property array<int,string> $signer_ids
 */
final class AddConsentResponse extends Struct
{
    use HasFactory;

    /** {@inheritdoc} */
    public $timestamps = false;

    /** {@inheritdoc} */
    protected $fillable = [
    ];

    /** {@inheritdoc} */
    protected $attributes = [
        'optional' => false,
    ];

    /** {@inheritdoc} */
    protected $casts = [
        'optional' => 'bool',
    ];

    /**
     * Set the proper factory for the model.
     *
     * @return AddConsentResponseFactory<self>
     */
    protected static function newFactory(): AddConsentResponseFactory
    {
        return AddConsentResponseFactory::new();
    }
}
