<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Libs\Soa\v1;

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
        $response = $this->apiClient->post(self::INITIATE_SIGNATURE_URL, $initiateSignatureRequest->toArray());

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

        /** @var string $url */
        $url = self::INITIATE_SIGNATURE_URL . self::URL_SEPARATOR . $signatureRequestId . self::URL_SEPARATOR . self::UPLOAD_DOCUMENT_URL;

        /** @var Response $response */
        $response = $this->apiClient->attach(
            self::FILE_PARAM,
            $uploadDocumentRequest->file_content,
            $uploadDocumentRequest->file_name,
        )
                                    ->post(
                                        $url,
                                        [
                                            self::NATURE_PARAM => $uploadDocumentRequest->nature->value,
                                        ]
                                    );

        if (!is_array($response->json())) {
            throw new RuntimeException('Yousign response is not an array.');
        }

        return new UploadDocumentResponse($response->json());
    }
}
