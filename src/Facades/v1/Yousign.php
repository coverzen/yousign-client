<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Facades\v1;

use Closure;
use Coverzen\Components\YousignClient\Fakes\v1\YousignFaker;
use Coverzen\Components\YousignClient\Libs\Soa\v1\Yousign as YousignSoaLib;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentResponse;
use Illuminate\Support\Facades\Facade;
use RuntimeException;

/**
 * Class facade.
 *
 * Facade to expose and fake Yousign soa lib.
 *
 * @see YousignSoaLib
 *
 * @method static InitiateSignatureResponse initiateSignature(InitiateSignatureRequest $initiateSignatureRequest)
 * @method static UploadDocumentResponse uploadDocument(string $signatureRequestId, UploadDocumentRequest $uploadDocumentRequest)
 * @method static void assertIsCalled(string $functionName, ?Closure $callback = null)
 */
final class Yousign extends Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @return void
     */
    public static function fake(): void
    {
        self::swap(new YousignFaker());
    }

    /**
     * Get the registered name of the component.
     *
     * @throws RuntimeException
     *
     * @return class-string<YousignSoaLib>
     */
    protected static function getFacadeAccessor(): string
    {
        return YousignSoaLib::class;
    }
}
