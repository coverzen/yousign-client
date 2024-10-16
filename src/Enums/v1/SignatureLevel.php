<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Enums\v1;

use Spatie\Enum\Laravel\Enum;

/**
 * Class SignatureLevel.
 *
 * @method static self electronic_signature()
 * @method static self advanced_electronic_signature()
 * @method static self electronic_signature_with_qualified_certificate()
 * @method static self qualified_electronic_signature()
 * @method static self qualified_electronic_signature_mode_1()
 */
final class SignatureLevel extends Enum
{
}
