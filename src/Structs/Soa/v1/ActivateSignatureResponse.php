<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\ActivateSignatureResponseFactory;
use Coverzen\Components\YousignClient\Enums\v1\ActivateSignatureResponseStatus;
use Coverzen\Components\YousignClient\Enums\v1\DeliveryMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * Class ActivateSignatureResponse.
 *
 * @property string|null $id
 * @property ActivateSignatureResponseStatus|null $status
 * @property string|null $name
 * @property DeliveryMode|null $delivery_mode
 * @property Carbon|null $created_at
 * @property bool $ordered_signers
 * @property array<array-key,mixed>|null $reminder_settings
 * @property string|null $timezone
 * @property Carbon|null $expiration_date
 * @property array<array-key,mixed> $signers
 * @property array<array-key,mixed> $approvers
 * @property array<array-key,mixed> $documents
 * @property string|null $external_id
 * @property string|null $branding_id
 * @property string|null $custom_experience_id
 * @property string|null $audit_trail_locale
 * @property bool $optional
 */
final class ActivateSignatureResponse extends Struct
{
    /** @use HasFactory<ActivateSignatureResponseFactory> */
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
        'signers' => [],
        'optional' => false,
    ];

    /** {@inheritdoc} */
    protected $casts = [
        'status' => ActivateSignatureResponseStatus::class,
        'delivery_mode' => DeliveryMode::class,
        'expiration_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Set the proper factory for the model.
     *
     * @return ActivateSignatureResponseFactory
     */
    protected static function newFactory(): ActivateSignatureResponseFactory
    {
        return ActivateSignatureResponseFactory::new();
    }
}
