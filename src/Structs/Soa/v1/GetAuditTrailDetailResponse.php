<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\GetAuditTrailDetailResponseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class GetAuditTrailDetailResponse.
 *
 * @property int|null $version
 * @property array<array-key,mixed>|null $signature_request
 * @property array<array-key,mixed>|null $organization
 * @property array<array-key,mixed>|null $sender
 * @property array<array-key,mixed>|null $signer
 * @property array<array-key,mixed>|null $authentication
 * @property array<array-key,mixed> $documents
 * @property array<array-key,mixed> $signer_consents
 */
final class GetAuditTrailDetailResponse extends Struct
{
    /** @use HasFactory<GetAuditTrailDetailResponseFactory> */
    use HasFactory;

    /** {@inheritdoc} */
    public $timestamps = false;

    /** {@inheritdoc} */
    protected $fillable = [
        'version',
        'signature_request',
        'organization',
        'sender',
        'signer',
        'authentication',
        'documents',
        'signer_consents',
    ];

    /** {@inheritdoc} */
    protected $attributes = [
        'documents' => [],
        'signer_consents' => [],
    ];

    /** {@inheritdoc} */
    protected $casts = [
        'signature_link_expiration_date' => 'datetime',
    ];

    /**
     * Set the proper factory for model.
     *
     * @return GetAuditTrailDetailResponseFactory<self>
     */
    protected static function newFactory(): GetAuditTrailDetailResponseFactory
    {
        return GetAuditTrailDetailResponseFactory::new();
    }
}
