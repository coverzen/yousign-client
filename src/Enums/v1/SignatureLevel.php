<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Enums\v1;

/**
 * Enum SignatureLevel.
 */
enum SignatureLevel: string
{
    case electronic_signature = 'electronic_signature';
    case advanced_electronic_signature = 'advanced_electronic_signature';
    case electronic_signature_with_qualified_certificate = 'electronic_signature_with_qualified_certificate';
    case qualified_electronic_signature = 'qualified_electronic_signature';
    case qualified_electronic_signature_mode_1 = 'qualified_electronic_signature_mode_1';
}
