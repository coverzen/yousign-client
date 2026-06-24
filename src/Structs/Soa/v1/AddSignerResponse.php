<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\AddSignerResponseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use function collect;

/**
 * Class AddSignerRequest.
 *
 * @property string|null $id
 * @property array<array-key,mixed> $info
 * @property string|null $status
 * @property string|null $signature_level
 * @property string|null $signature_authentication_mode
 * @property string|null $signature_link
 * @property array<int,SignerField> $fields
 * @property Carbon|null $signature_link_expiration_date
 * @property string|null $signature_image_preview
 * @property array<array-key,mixed> $redirect_urls
 * @property array<array-key,mixed> $custom_text
 * @property string|null $delivery_mode
 * @property string|null $identification_attestation_id
 * @property array<array-key,mixed> $sms_notification
 */
final class AddSignerResponse extends Struct
{
    /** @use HasFactory<AddSignerResponseFactory> */
    use HasFactory;

    /** {@inheritdoc} */
    public $timestamps = false;

    /** {@inheritdoc} */
    protected $fillable = [
        'id',
        'info',
        'status',
        'signature_level',
        'signature_authentication_mode',
        'signature_link',
        'fields',
        'signature_link_expiration_date',
        'signature_image_preview',
        'redirect_urls',
        'custom_text',
        'delivery_mode',
        'identification_attestation_id',
        'sms_notification',
    ];

    /** {@inheritdoc} */
    protected $attributes = [
        'info' => [],
        'fields' => [],
        'redirect_urls' => [],
        'custom_text' => [],
        'sms_notification' => [],
    ];

    /** {@inheritdoc} */
    protected $casts = [
        'signature_link_expiration_date' => 'datetime',
    ];

    /**
     * @param array<string,mixed> $attributes
     */
    public function __construct(array $attributes = [])
    {
        /** @var array<int,array<array-key,mixed>|SignerField> $fields */
        $fields = Arr::get($attributes, 'fields', []);

        /** @var array<int,SignerField> $mappedFields */
        $mappedFields = collect($fields)->map(
            static function (SignerField|array $field) {
                if ($field instanceof SignerField) {
                    return $field;
                }

                return new SignerField($field);
            }
        )->all();

        Arr::set($attributes, 'fields', $mappedFields);

        parent::__construct($attributes);
    }

    /**
     * Set the proper factory for model.
     *
     * @return AddSignerResponseFactory<self>
     */
    protected static function newFactory(): AddSignerResponseFactory
    {
        return AddSignerResponseFactory::new();
    }
}
