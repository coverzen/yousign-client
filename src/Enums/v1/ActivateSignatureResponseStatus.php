<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Enums\v1;

/**
 * Enum ActivateSignatureResponseStatus.
 */
enum ActivateSignatureResponseStatus: string
{
    case ongoing = 'ongoing';
    case approval = 'approval';
}
