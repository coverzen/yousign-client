<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Libs\Soa\v1;

use Coverzen\Components\YousignClient\Structs\Soa\v1\ActivateSignatureResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\GetAuditTrailDetailResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\GetConsentsResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentResponse;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use function base64_decode;
use function base64_encode;
use function implode;
use function is_array;

/**
 * Class Yousign.
 */
class Yousign extends Soa
{
    /** @var string */
    public const SIGNATURE_REQUESTS_BASE_URL = 'signature_requests';

    /** @var string */
    public const UPLOAD_DOCUMENT_URL = 'documents';

    /** @var string */
    public const DOWNLOAD_URL = 'download';

    /** @var string */
    public const DOWNLOAD_DOCUMENT_URL = 'documents/download';

    /** @var string */
    public const DOWNLOAD_AUDIT_TRAIL = 'audit_trails/download';

    /** @var string */
    public const GET_AUDIT_TRAIL_DETAIL = 'audit_trails';

    /** @var string */
    public const ADD_CONSENT_URL = 'consent_requests';

    /** @var string */
    public const SIGNER_URL = 'signers';

    /** @var string */
    public const ACTIVATE_SIGNATURE_URL = 'activate';

    /** @var string */
    public const FILE_PARAM = 'file';

    /** @var string */
    public const NATURE_PARAM = 'nature';

    /**
     * Http API Client for requests to Yousign.
     *
     * @var PendingRequest
     */
    private PendingRequest $apiClient;

    /**
     * Yousign SOA lib constructor.
     *
     * @psalm-suppress PossiblyNullArgument
     */
    public function __construct()
    {
        parent::__construct();

        $this->apiClient = Http::connectTimeout(Arr::get($this->config, 'connection_timeout_seconds', 0))
                               ->timeout(Arr::get($this->config, 'timeout_seconds', 0))
                               ->baseUrl(Str::finish(Arr::get($this->config, 'url'), Soa::URL_SEPARATOR))
                               ->acceptJson()
                               ->withHeader(Soa::AUTHORIZATION_HEADER, self::BEARER_PREFIX . Arr::get($this->config, 'api_key'))
                               ->retry(
                                   Arr::get($this->config, 'retry_count', 1),
                                   fn (int $attempt): int => $this->calculateBackoff($attempt),
                                   fn (Exception $exception): bool => $this->manageRetry($exception)
                               )
                               ->throw(fn (Response $response, RequestException $e) => $this->logClientFailure('Yousign api returns a wrong response', $response, $e));
    }

    /**
     * @param InitiateSignatureRequest $initiateSignatureRequest
     *
     * @return InitiateSignatureResponse
     */
    public function initiateSignature(InitiateSignatureRequest $initiateSignatureRequest): InitiateSignatureResponse
    {
        /** @var Response $response */
        $response = $this->apiClient->post(self::SIGNATURE_REQUESTS_BASE_URL, $initiateSignatureRequest->payload);

        if (!is_array($response->json())) {
            throw new RuntimeException('Yousign response is not an array.');
        }

        return new InitiateSignatureResponse($response->json());
    }

    /**
     * @param string $signatureRequestId
     * @param UploadDocumentRequest $uploadDocumentRequest
     *
     * @return UploadDocumentResponse
     */
    public function uploadDocument(string $signatureRequestId, UploadDocumentRequest $uploadDocumentRequest): UploadDocumentResponse
    {
        if (!$uploadDocumentRequest->file_content) {
            throw new RuntimeException('File content is required.');
        }

        /**
         * The client is cloned so that the `attach()` method doesn't change headers for all the following requests.
         *
         * @var PendingRequest $apiClientWithAttach
         */
        $apiClientWithAttach = clone $this->apiClient;

        if (self::isBase64($uploadDocumentRequest->file_content)) {
            $uploadDocumentRequest->file_content = base64_decode($uploadDocumentRequest->file_content, true);
        }

        /** @var Response $response */
        $response = $apiClientWithAttach->attach(
            self::FILE_PARAM,
            $uploadDocumentRequest->file_content,
            $uploadDocumentRequest->file_name,
        )
                                        ->post(
                                            implode(
                                                self::URL_SEPARATOR,
                                                [
                                                    self::SIGNATURE_REQUESTS_BASE_URL,
                                                    $signatureRequestId,
                                                    self::UPLOAD_DOCUMENT_URL,
                                                ]
                                            ),
                                            [
                                                self::NATURE_PARAM => $uploadDocumentRequest->nature->value,
                                            ]
                                        );

        if (!is_array($response->json())) {
            throw new RuntimeException('Yousign response is not an array.');
        }

        return new UploadDocumentResponse($response->json());
    }

    /**
     * @param string $signatureRequestId
     * @param AddSignerRequest $addSignerRequest
     *
     * @return AddSignerResponse
     */
    public function addSigner(string $signatureRequestId, AddSignerRequest $addSignerRequest): AddSignerResponse
    {
        /** @var string $url */
        $url = implode(
            self::URL_SEPARATOR,
            [
                self::SIGNATURE_REQUESTS_BASE_URL,
                $signatureRequestId,
                self::SIGNER_URL,
            ]
        );

        /** @var Response $response */
        $response = $this->apiClient->post($url, $addSignerRequest->payload);

        if (!is_array($response->json())) {
            throw new RuntimeException('Yousign response is not an array.');
        }

        return new AddSignerResponse($response->json());
    }

    /**
     * @param string $signatureRequestId
     * @param AddConsentRequest $addConsentRequest
     *
     * @return AddConsentResponse
     */
    public function addConsent(string $signatureRequestId, AddConsentRequest $addConsentRequest): AddConsentResponse
    {
        /** @var Response $response */
        $response = $this->apiClient->post(
            implode(
                self::URL_SEPARATOR,
                [
                    self::SIGNATURE_REQUESTS_BASE_URL,
                    $signatureRequestId,
                    self::ADD_CONSENT_URL,
                ]
            ),
            $addConsentRequest->toArray()
        );

        if (!is_array($response->json())) {
            throw new RuntimeException('Yousign response is not an array.');
        }

        return new AddConsentResponse($response->json());
    }

    /**
     * @param string $signatureRequestId
     *
     * @return ActivateSignatureResponse
     */
    public function activateSignature(string $signatureRequestId): ActivateSignatureResponse
    {
        /** @var string $url */
        $url = implode(
            self::URL_SEPARATOR,
            [
                self::SIGNATURE_REQUESTS_BASE_URL,
                $signatureRequestId,
                self::ACTIVATE_SIGNATURE_URL,
            ]
        );

        /** @var Response $response */
        $response = $this->apiClient->post($url);

        if (!is_array($response->json())) {
            throw new RuntimeException('Yousign response is not an array.');
        }

        return new ActivateSignatureResponse($response->json());
    }

    /**
     * @param string $signatureRequestId
     *
     * @return InitiateSignatureResponse
     */
    public function getSignatureById(string $signatureRequestId): InitiateSignatureResponse
    {
        /** @var string $url */
        $url = self::SIGNATURE_REQUESTS_BASE_URL . self::URL_SEPARATOR . $signatureRequestId;

        /** @var Response $response */
        $response = $this->apiClient->get($url);

        if (!is_array($response->json())) {
            throw new RuntimeException('Yousign response is not an array.');
        }

        return new InitiateSignatureResponse($response->json());
    }

    /**
     * @param string $signatureRequestId
     * @param string $documentId
     *
     * @return string
     */
    public function getDocumentById(string $signatureRequestId, string $documentId): string
    {
        /** @var string $url */
        $url = implode(
            self::URL_SEPARATOR,
            [
                self::SIGNATURE_REQUESTS_BASE_URL,
                $signatureRequestId,
                self::UPLOAD_DOCUMENT_URL,
                $documentId,
                self::DOWNLOAD_URL,
            ]
        );

        /** @var Response $response */
        $response = $this->apiClient->get($url);

        if (!is_string($response->body())) {
            throw new RuntimeException('Yousign response is not a string.');
        }

        return $response->body();
    }

    /**
     * @param string $signatureRequestId
     * @param string $signerId
     *
     * @return string
     */
    public function getAuditTrail(string $signatureRequestId, string $signerId): string
    {
        /** @var string $url */
        $url = implode(
            self::URL_SEPARATOR,
            [
                self::SIGNATURE_REQUESTS_BASE_URL,
                $signatureRequestId,
                self::SIGNER_URL,
                $signerId,
                self::DOWNLOAD_AUDIT_TRAIL,
            ]
        );

        /** @var Response $response */
        $response = $this->apiClient->get($url);

        if (!is_string($response->body())) {
            throw new RuntimeException('Yousign response is not a string.');
        }

        return $response->body();
    }

    /**
     * @param string $signatureRequestId
     * @param string $signerId
     *
     * @return GetAuditTrailDetailResponse
     */
    public function getAuditTrailDetail(string $signatureRequestId, string $signerId): GetAuditTrailDetailResponse
    {
        /** @var string $url */
        $url = implode(
            self::URL_SEPARATOR,
            [
                self::SIGNATURE_REQUESTS_BASE_URL,
                $signatureRequestId,
                self::SIGNER_URL,
                $signerId,
                self::GET_AUDIT_TRAIL_DETAIL,
            ]
        );

        /** @var Response $response */
        $response = $this->apiClient->get($url);

        if (!is_array($response->json())) {
            throw new RuntimeException('Yousign response is not an array.');
        }

        return new GetAuditTrailDetailResponse($response->json());
    }

    /**
     * @param string $signatureRequestId
     *
     * @return GetConsentsResponse
     */
    public function getConsentsById(string $signatureRequestId): GetConsentsResponse
    {
        /** @var string $url */
        $url = implode(
            self::URL_SEPARATOR,
            [
                self::SIGNATURE_REQUESTS_BASE_URL,
                $signatureRequestId,
                self::ADD_CONSENT_URL,
            ]
        );

        /** @var Response $response */
        $response = $this->apiClient->get($url);

        if (!is_array($response->json())) {
            throw new RuntimeException('Yousign response is not an array.');
        }

        return new GetConsentsResponse($response->json());
    }

    /**
     * Quick way to check if a string is base64 encoded.
     *
     * @param string $str
     *
     * @return bool
     */
    private static function isBase64(string $str): bool
    {
        return base64_encode((string) base64_decode($str, true)) === $str;
    }
}
