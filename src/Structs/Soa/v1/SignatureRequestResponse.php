<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\SignatureRequestResponseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * Class SignatureRequestResponse.
 *
 * @property string|null $id
 * @property string|null $source
 * @property string|null $status
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property string|null $email_custom_note
 * @property bool $ordered_signers
 * @property string|null $timezone
 * @property array<string,mixed>|null $reminder_settings
 * @property Carbon|null $expiration_date
 * @property string|null $delivery_mode
 * @property array<int,array<string,mixed>> $documents
 * @property array<int,array<string,mixed>> $signers
 * @property string|null $external_id
 * @property string|null $branding_id
 * @property string|null $custom_experience_id
 * @property array<string,mixed>|null $sender
 * @property string|null $workspace_id
 * @property string|null $audit_trail_locale
 * @property bool $signers_allowed_to_decline
 * @property string|null $bulk_send_batch_id
 * @property array<string,mixed>|null $email_notification
 */
class SignatureRequestResponse extends Struct
{
    /** @use HasFactory<SignatureRequestResponseFactory> */
    use HasFactory;

    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /** {@inheritdoc} */
    protected $fillable = [
        'id',
        'source',
        'status',
        'name',
        'created_at',
        'email_custom_note',
        'ordered_signers',
        'timezone',
        'reminder_settings',
        'expiration_date',
        'delivery_mode',
        'documents',
        'signers',
        'external_id',
        'branding_id',
        'custom_experience_id',
        'sender',
        'workspace_id',
        'audit_trail_locale',
        'signers_allowed_to_decline',
        'bulk_send_batch_id',
        'email_notification',
    ];

    /** {@inheritdoc} */
    protected $attributes = [
        'ordered_signers' => false,
        'signers_allowed_to_decline' => false,
        'signers' => [],
        'documents' => [],
    ];

    /** {@inheritdoc} */
    protected $casts = [
        'ordered_signers' => 'boolean',
        'signers_allowed_to_decline' => 'boolean',
        'created_at' => 'datetime',
        'expiration_date' => 'datetime',
    ];

    /**
     * Set the proper factory for model.
     *
     * @return SignatureRequestResponseFactory
     */
    protected static function newFactory(): SignatureRequestResponseFactory
    {
        return SignatureRequestResponseFactory::new();
    }
}
