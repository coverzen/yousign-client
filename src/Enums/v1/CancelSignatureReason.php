<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Enums\v1;

/**
 * Enum CancelSignatureReason.
 */
enum CancelSignatureReason: string
{
    case contractualization_aborted = 'contractualization_aborted';
    case errors_in_document = 'errors_in_document';
    case other = 'other';
}
