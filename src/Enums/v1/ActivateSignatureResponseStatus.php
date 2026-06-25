<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Enums\v1;

/**
 * Enum ActivateSignatureResponseStatus.
 */
enum ActivateSignatureResponseStatus: string
{
    case draft = 'draft';
    case ongoing = 'ongoing';
    case done = 'done';
    case expired = 'expired';
    case canceled = 'canceled';
    case rejected = 'rejected';
    case deleted = 'deleted';
    case approval = 'approval';
}
