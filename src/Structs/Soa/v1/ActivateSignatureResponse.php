<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\ActivateSignatureResponseFactory;
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
        'id',
        'status',
        'name',
        'delivery_mode',
        'created_at',
        'ordered_signers',
        'reminder_settings',
        'timezone',
        'expiration_date',
        'signers',
        'approvers',
        'documents',
        'external_id',
        'branding_id',
        'custom_experience_id',
        'audit_trail_locale',
    ];

    /** {@inheritdoc} */
    protected $attributes = [
    ];

    /** {@inheritdoc} */
    protected $casts = [
        'expiration_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Set the proper factory for the model.
     *
     * @return ActivateSignatureResponseFactory<self>
     */
    protected static function newFactory(): ActivateSignatureResponseFactory
    {
        return ActivateSignatureResponseFactory::new();
    }
}
