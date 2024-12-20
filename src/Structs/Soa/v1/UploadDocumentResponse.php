<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\UploadDocumentResponseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * Class UploadDocumentResponse.
 *
 * @property string|null $id
 * @property string|null $filename
 * @property string|null $nature
 * @property string|null $content_type
 * @property string|null $sha256
 * @property bool $is_protected
 * @property bool $is_signed
 * @property Carbon|null $created_at
 * @property int $total_pages
 * @property bool $is_locked
 * @property string|null $initials
 * @property int $total_anchors
 */
class UploadDocumentResponse extends Struct
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $fillable = [
        'id',
        'filename',
        'nature',
        'content_type',
        'sha256',
        'is_protected',
        'is_signed',
        'created_at',
        'total_pages',
        'is_locked',
        'initials',
        'total_anchors',
    ];

    /** {@inheritdoc} */
    protected $attributes = [
        'is_protected' => false,
        'is_signed' => false,
        'is_locked' => false,
        'total_pages' => 0,
        'total_anchors' => 0,
    ];

    /** {@inheritdoc} */
    protected $casts = [
        'is_protected' => 'boolean',
        'is_signed' => 'boolean',
        'is_locked' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Set the proper factory for model.
     *
     * @return UploadDocumentResponseFactory<self>
     */
    protected static function newFactory(): UploadDocumentResponseFactory
    {
        return UploadDocumentResponseFactory::new();
    }
}
