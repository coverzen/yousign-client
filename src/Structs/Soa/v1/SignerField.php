<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\SignerFieldFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class SignerField.
 *
 * // * @property string|null $retro_activity
 * // * @property string|null $warranty
 * // * @property int $coverage_cap
 * // * @property bool $joint_sentence
 */

/**
 * Class SignerField.
 *
 * @property string $document_id
 * @property string|null $type
 * @property int|null $height
 * @property int|null $width
 * @property int|null $page
 * @property int|null $x
 * @property int|null $y
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
