<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Enums\v1;

/**
 * Enum DocumentNature.
 */
enum DocumentNature: string
{
    case attachment = 'attachment';
    case signable_document = 'signable_document';
}
