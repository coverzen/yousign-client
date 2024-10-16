<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Enums\v1;

use Spatie\Enum\Laravel\Enum;

/**
 * Class SignatureAuthenticationMode.
 *
 * @method static self otp_email()
 * @method static self otp_sms()
 * @method static self no_otp()
 */
final class SignatureAuthenticationMode extends Enum
{
}
