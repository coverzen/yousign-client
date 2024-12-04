<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\AddSignerRequestFactory;
use Coverzen\Components\YousignClient\Enums\v1\SignatureAuthenticationMode;
use Coverzen\Components\YousignClient\Enums\v1\SignatureLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class AddSignerRequest.
 *
 * @property array $info
 * @property SignatureLevel $signature_level
 * @property SignatureAuthenticationMode|null $signature_authentication_mode
 * @property array<int,SignerField> $fields
 * @property-read array $payload
 */
final class AddSignerRequest extends Request
{
    use HasFactory;

    /** {@inheritdoc} */
    public $timestamps = false;

    /** {@inheritdoc} */
    protected $attributes = [
        'info' => [],
    ];

    /** {@inheritdoc} */
    protected $casts = [
        'signature_level' => SignatureLevel::class,
        'signature_authentication_mode' => SignatureAuthenticationMode::class . ':nullable',
    ];

    /** {@inheritdoc} */
    protected $fillable = [
        'info',
        'signature_level',
        'signature_authentication_mode',
        'fields',
    ];

    /**
     * Set the proper factory for model.
     *
     * @return AddSignerRequestFactory<self>
     */
    protected static function newFactory(): AddSignerRequestFactory
    {
        return AddSignerRequestFactory::new();
    }
}
