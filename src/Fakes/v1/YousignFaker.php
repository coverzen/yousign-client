<?php declare(strict_types=1);

namespace Coverzen\Components\YousignClient\Fakes\v1;

use Closure;
use Coverzen\Components\YousignClient\Libs\Soa\v1\Soa;
use Coverzen\Components\YousignClient\Libs\Soa\v1\Yousign;
use Coverzen\Components\YousignClient\Structs\Soa\v1\AddConsentRequest;
use Coverzen\Components\YousignClient\Structs\Soa\v1\InitiateSignatureResponse;
use Coverzen\Components\YousignClient\Structs\Soa\v1\UploadDocumentResponse;
use Coverzen\Components\YousignClient\YousignClientServiceProvider;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\ExpectationFailedException;
use RuntimeException;
use function app;
use function sprintf;

/**
 * Class YousignFaker.
 *
 * This class is swapped behind Yousign facade if `Yousign::fake()` method is
 * called. It provides a series of assertion for tests and blocks sending HTTP
 * requests outwards.
 */
class YousignFaker
{
    /**
     * Failed assertion message.
     *
     * @var string
     */
    public const FAILED_ASSERTION_MESSAGE_DIFFERENT_FUNCTION = 'Failed asserting expected called function "%s" matches actual: "%s"';

    /**
     * Failed assertion message.
     *
     * @var string
     */
    public const FAILED_ASSERTION_MESSAGE_NO_CALLED = 'Failed asserting expected called function "%s" is called';

    /**
     * Name of the last called function.
     *
     * @see self::assertIsCalled()
     *
     * @var string|null
     */
    private ?string $calledFunctionName = null;

    /**
     * Arguments of the last called function.
     *
     * @see self::assertIsCalled()
     *
     * @var array<int,mixed>
     */
    private array $arguments = [];

    /**
     * Constructor for `YousignFaker` class.
     */
    public function __construct()
    {
        if (!app()->runningUnitTests()) {
            throw new RuntimeException('YousignFaker should only be used in tests');
        }

        Http::preventStrayRequests();

        /** @var string $url */
        $url = Str::finish(Config::get(YousignClientServiceProvider::CONFIG_KEY . '.url'), Soa::URL_SEPARATOR);

        Http::fake(
            [
                $url . Yousign::INITIATE_SIGNATURE_URL => Http::response(
                    InitiateSignatureResponse::factory()
                                             ->make()
                                             ->toArray(),
                    Response::HTTP_CREATED
                ),
                $url . Yousign::INITIATE_SIGNATURE_URL . '/*/' . Yousign::UPLOAD_DOCUMENT_URL => Http::response(
                    UploadDocumentResponse::factory()
                                          ->make()
                                          ->toArray(),
                    Response::HTTP_CREATED
                ),
                $url . Yousign::INITIATE_SIGNATURE_URL . '/*/' . Yousign::ADD_CONSENT_URL => Http::response(
                    AddConsentRequest::factory()
                                          ->make()
                                          ->toArray(),
                    Response::HTTP_CREATED
                ),
            ]
        );
    }

    /**
     * @param string $function
     * @param array<int,mixed> $arguments
     *
     * @return mixed
     */
    public function __call(string $function, array $arguments): mixed
    {
        $this->calledFunctionName = $function;
        $this->arguments = $arguments;

        return (new Yousign())->{$function}(...$arguments);
    }

    /**
     * Function to assert a facade method is called.
     * If a callback is provided, it will be called with the arguments of the
     * called function.
     *
     * @param string $expectedFunctionName
     * @param Closure|null $callback
     *
     * @return void
     */
    public function assertIsCalled(string $expectedFunctionName, ?Closure $callback = null): void
    {
        if (null === $this->calledFunctionName) {
            throw new ExpectationFailedException(sprintf(self::FAILED_ASSERTION_MESSAGE_NO_CALLED, $expectedFunctionName));
        }

        PHPUnit::assertSame(
            $expectedFunctionName,
            $this->calledFunctionName,
            sprintf(self::FAILED_ASSERTION_MESSAGE_DIFFERENT_FUNCTION, $expectedFunctionName, $this->calledFunctionName)
        );

        $callback = $callback ?: static fn (): bool => true;

        PHPUnit::assertTrue($callback(...$this->arguments));
    }
}
