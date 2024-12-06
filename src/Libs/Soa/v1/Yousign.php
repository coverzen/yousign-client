<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Libs\Soa\v1;

use Coverzen\Components\YousignClient\Structs\Soa\v1\ActivateSignatureResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddSignerResponse;
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
use function implode;
use function is_array;

/**
 * Class Yousign.
 */
class Yousign extends Soa
{
    /** @var string */
    public const INITIATE_SIGNATURE_URL = 'signature_requests';

    /** @var string */
    public const UPLOAD_DOCUMENT_URL = 'documents';

    /** @var string */
    public const DOWNLOAD_DOCUMENT_URL = 'documents/download';

    /** @var string */
    public const DOWNLOAD_AUDIT_TRAIL = 'audit_trails/download';

    /** @var string */
    public const ADD_CONSENT_URL = 'consent_requests';

    /** @var string */
    public const ADD_SIGNER_URL = 'signers';

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
        $response = $this->apiClient->post(self::INITIATE_SIGNATURE_URL, $initiateSignatureRequest->payload);

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
         * this variable is cloned so that the attach method doesn't change the content type header for the following requests.
         *
         * @var PendingRequest $apiClientWithAttach
         */
        $apiClientWithAttach = clone $this->apiClient;

        if (!base64_decode($uploadDocumentRequest->file_content, true)) {
            throw new RuntimeException('File content not valid.');
        }

        /** @var Response $response */
        $response = $apiClientWithAttach->attach(
            self::FILE_PARAM,
            base64_decode($uploadDocumentRequest->file_content, true),
            $uploadDocumentRequest->file_name,
        )
                                    ->post(
                                        implode(
                                            self::URL_SEPARATOR,
                                            [
                                                self::INITIATE_SIGNATURE_URL,
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
        $url = self::INITIATE_SIGNATURE_URL . self::URL_SEPARATOR . $signatureRequestId . self::URL_SEPARATOR . self::ADD_SIGNER_URL;

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
                    self::INITIATE_SIGNATURE_URL,
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
        $url = self::INITIATE_SIGNATURE_URL . self::URL_SEPARATOR . $signatureRequestId . self::URL_SEPARATOR . self::ACTIVATE_SIGNATURE_URL;

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
        $url = self::INITIATE_SIGNATURE_URL . self::URL_SEPARATOR . $signatureRequestId;

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
        $url = self::INITIATE_SIGNATURE_URL . self::URL_SEPARATOR . $signatureRequestId . self::URL_SEPARATOR . self::UPLOAD_DOCUMENT_URL . self::URL_SEPARATOR . $documentId;

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
        $url = self::INITIATE_SIGNATURE_URL . self::URL_SEPARATOR . $signatureRequestId . self::URL_SEPARATOR . self::ADD_SIGNER_URL . Soa::URL_SEPARATOR . $signerId . self::URL_SEPARATOR . self::DOWNLOAD_AUDIT_TRAIL;

        /** @var Response $response */
        $response = $this->apiClient->get($url);

        if (!is_string($response->body())) {
            throw new RuntimeException('Yousign response is not a string.');
        }

        return $response->body();
    }

    /**
     * @param string $signatureRequestId
     *
     * @return GetConsentsResponse
     */
    public function getConsentsById(string $signatureRequestId): GetConsentsResponse
    {
        /** @var string $url */
        $url = self::INITIATE_SIGNATURE_URL . self::URL_SEPARATOR . $signatureRequestId . self::URL_SEPARATOR . self::ADD_CONSENT_URL;

        /** @var Response $response */
        $response = $this->apiClient->get($url);

        if (!is_array($response->json())) {
            throw new RuntimeException('Yousign response is not an array.');
        }

        return new GetConsentsResponse($response->json());
    }
}
