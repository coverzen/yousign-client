<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\InitiateSignatureRequestFactory;
use Coverzen\Components\YousignClient\Enums\v1\DeliveryMode;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CreateProcedureRequest.
 *
 * @property string|null $name
 * @property DeliveryMode $delivery_mode
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
        'ordered_signers' => false,
        'timezone' => null,
    ];

    /** {@inheritdoc} */
    protected $casts = [
        'delivery_mode' => DeliveryMode::class,
        'ordered_signers' => 'boolean',
    ];

    /**
     * @param array<array-key,mixed> $attributes
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->delivery_mode = DeliveryMode::none();

        parent::__construct($attributes);
    }

    /**
     * Set the proper factory for the model.
     *
     * @return InitiateSignatureRequestFactory<self>
     */
    protected static function newFactory(): InitiateSignatureRequestFactory
    {
        return InitiateSignatureRequestFactory::new();
    }
}
