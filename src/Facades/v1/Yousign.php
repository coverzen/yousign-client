<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Facades\v1;

use Closure;
use Coverzen\Components\YousignClient\Fakes\v1\YousignFaker;
use Coverzen\Components\YousignClient\Libs\Soa\v1\Yousign as YousignSoaLib;
use Coverzen\Components\YousignClient\Structs\Soa\v1\ActivateSignatureResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\CancelSignatureRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\GetAuditTrailDetailResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\GetConsentsResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\SignatureRequestResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentResponse;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Facade;
use RuntimeException;

/**
 * Class facade.
 *
 * Facade to expose and fake Yousign soa lib.
 *
 * @see YousignSoaLib
 *
 * @method static SignatureRequestResponse initiateSignature(InitiateSignatureRequest $initiateSignatureRequest)
 * @method static UploadDocumentResponse uploadDocument(string $signatureRequestId, UploadDocumentRequest $uploadDocumentRequest)
 * @method static AddConsentResponse addConsent(string $signatureRequestId, AddConsentRequest $addConsentRequest)
 * @method static AddSignerResponse addSigner(string $signatureRequestId, AddSignerRequest $addSignerRequest)
 * @method static ActivateSignatureResponse activateSignature(string $signatureRequestId)
 * @method static SignatureRequestResponse getSignatureById(string $signatureRequestId)
 * @method static string getDocumentById(string $signatureRequestId, string $documentId)
 * @method static string getAuditTrail(string $signatureRequestId, string $signerId)
 * @method static GetConsentsResponse getConsentsById(string $signatureRequestId)
 * @method static GetAuditTrailDetailResponse getAuditTrailDetail(string $signatureRequestId, string $signerId)
 * @method static SignatureRequestResponse cancelSignatureRequest(string $signatureRequestId, CancelSignatureRequest $cancelSignatureRequest)
 * @method static Response deleteSignatureRequest(string $signatureRequestId)
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
