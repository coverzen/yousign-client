<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\InitiateSignatureRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CreateProcedureRequest.
 *
 * @property string|null $name
 * @property string|null $delivery_mode
 * @property bool $ordered_signers
 * @property string|null $timezone
 */
final class InitiateSignatureRequest extends Struct
{
    use HasFactory;

    /** {@inheritdoc} */
    public $timestamps = false;

    /** {@inheritdoc} */
    protected $fillable = [
        'name',
        'delivery_mode',
        'ordered_signers',
        'timezone',
    ];

    /** {@inheritdoc} */
    protected $attributes = [
        'name' => null,
        'delivery_mode' => null,
        'ordered_signers' => false,
        'timezone' => null,
    ];

    /** {@inheritdoc} */
    protected $casts = [
        'ordered_signers' => 'boolean',
    ];

    /**
     * Set the proper factory for model.
     *
     * @return InitiateSignatureRequestFactory<self>
     */
    protected static function newFactory(): InitiateSignatureRequestFactory
    {
        return InitiateSignatureRequestFactory::new();
    }
}
