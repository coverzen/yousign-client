<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Enums\v1;

/**
 * Enum SignatureAuthenticationMode.
 */
enum SignatureAuthenticationMode: string
{
    case otp_email = 'otp_email';
    case otp_sms = 'otp_sms';
    case no_otp = 'no_otp';
}
