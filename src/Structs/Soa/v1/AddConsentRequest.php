<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\AddConsentRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class AddConsentRequest.
 *
 * @property string|null $type
 * @property array|null $settings
 * @property bool $optional
 * @property array<int,string> $signer_ids
 */
final class AddConsentRequest extends Struct
{
    use HasFactory;

    /** @var string */
    public const DEFAULT_TYPE = 'checkbox';

    /** {@inheritdoc} */
    protected $fillable = [
        'type',
        'settings',
        'optional',
        'signer_ids',
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
     * @return AddConsentRequestFactory<self>
     */
    protected static function newFactory(): AddConsentRequestFactory
    {
        return AddConsentRequestFactory::new();
    }
}
