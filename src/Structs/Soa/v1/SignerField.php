<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\SignerFieldFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class SignerField.
 *
 * @property string $document_id
 * @property string $type
 * @property int|null $height
 * @property int|null $width
 * @property int $page
 * @property int $x
 * @property int $y
 */
final class SignerField extends Struct
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $fillable = [
        'document_id',
        'type',
        'height',
        'width',
        'page',
        'x',
        'y',
    ];

    /** @var array<string,mixed> */
    protected $attributes = [
    ];

    /** {@inheritdoc} */
    protected $casts = [
    ];

    /**
     * Set the proper factory for model.
     *
     * @return SignerFieldFactory<self>
     */
    protected static function newFactory(): SignerFieldFactory
    {
        return SignerFieldFactory::new();
    }
}
