<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Structs\Soa\v1;

use Coverzen\Components\YousignClient\Database\Factories\v1\UploadDocumentRequestFactory;
use Coverzen\Components\YousignClient\Enums\v1\DocumentNature;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class UploadDocumentRequest.
 *
 * @property string|null $file_content
 * @property string|null $file_name
 * @property DocumentNature $nature
 * @property-read array $payload
 */
final class UploadDocumentRequest extends Request
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $casts = [
        'nature' => DocumentNature::class,
    ];

    /** {@inheritdoc} */
    protected $fillable = [
        'file_content',
        'file_name',
        'nature',
    ];

    /**
     * Set the proper factory for model.
     *
     * @return UploadDocumentRequestFactory<self>
     */
    protected static function newFactory(): UploadDocumentRequestFactory
    {
        return UploadDocumentRequestFactory::new();
    }
}
