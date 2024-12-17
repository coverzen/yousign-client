<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\CancelSignatureRequestFactory;
use Coverzen\Components\YousignClient\Enums\v1\CancelSignatureReason;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class CancelSignatureRequest.
 *
 * @property CancelSignatureReason $reason
 * @property string|null $custom_note
 */
final class CancelSignatureRequest extends Struct
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $fillable = [
        'reason',
        'custom_note',
    ];

    /** {@inheritdoc} */
    protected $casts = [
        'reason' => CancelSignatureReason::class,
    ];

    /**
     * @param array<array-key,mixed> $attributes
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->reason = CancelSignatureReason::contractualization_aborted();
    }

    /**
     * Set the proper factory for the model.
     *
     * @return CancelSignatureRequestFactory<self>
     */
    protected static function newFactory(): CancelSignatureRequestFactory
    {
        return CancelSignatureRequestFactory::new();
    }
}
