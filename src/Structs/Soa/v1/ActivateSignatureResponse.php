<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\ActivateSignatureResponseFactory;
use Coverzen\Components\YousignClient\Database\Factories\v1\AddConsentResponseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class ActivateSignatureResponse.
 *
 * @property string|null $id
 * @property string|null $type
 * @property array|null $settings
 * @property bool $optional
 * @property array<int,string> $signer_ids
 */
final class ActivateSignatureResponse extends Struct
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $fillable = [
    ];

    /** {@inheritdoc} */
    protected $attributes = [
    ];

    /** {@inheritdoc} */
    protected $casts = [
    ];

    /**
     * Set the proper factory for the model.
     *
     * @return ActivateSignatureResponseFactory<self>
     */
    protected static function newFactory(): AddConsentResponseFactory
    {
        return ActivateSignatureResponseFactory::new();
    }
}
