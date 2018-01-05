<?php
namespace Aws\S3;

use Aws\CommandInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Used to update the URL used for S3 requests to support:
 * S3 Accelerate, S3 DualStack or Both
 *
 * IMPORTANT: this middleware must be added after the "build" step.
 *
 * @internal
 */
class S3EndpointMiddleware
{
    private static $exclusions = [
        'CreateBucket' => true,
        'DeleteBucket' => true,
        'ListBuckets' => true,
    ];

    const NO_PATTERN = 0;
    const DUALSTACK = 1;
    const ACCELERATE = 2;
    const ACCELERATE_DUALSTACK = 3;

    /** @var bool */
    private $accelerateByDefault;
    /** @var bool */
    private $dualStackByDefault;
    /** @var string */
    private $region;
    /** @var callable */
    private $nextHandler;

    /**
     * Create a middleware wrapper function
     *
     * @param string $region
     * @param array  $options
     *
     * @return callable
     */
    public static function wrap($region, array $options)
    {
        return function (callable $handler) use ($region, $options) {
            return new self($handler, $region, $options);
        };
    }

    public function __construct(
        callable $nextHandler,
        $region,
        array $options
    ) {
        $this->dualStackByDefault = isset($options['dual_stack'])
            ? (bool) $options['dual_stack'] : false;
        $this->accelerateByDefault = isset($options['accelerate'])
            ? (bool) $options['accelerate'] : false;
        $this->region = (string) $region;
        $this->nextHandler = $nextHandler;
    }

    public function __invoke(CommandInterface $command, RequestInterface $request)
    {
        $endpointPattern = $this->endpointPatternDecider($command);
        switch ($endpointPattern) {
            case self::NO_PATTERN:
                break;
            case self::DUALSTACK:
                $request = $this->applyDualStackEndpoint($request);
                break;
            case self::ACCELERATE:
                $request = $this->applyAccelerateEndpoint(
                    $command,
                    $request,
                    's3-accelerate'
                );
                break;
            case self::ACCELERATE_DUALSTACK:
                $request = $this->applyAccelerateEndpoint(
                    $command,
                    $request,
                    's3-accelerate.dualstack'
                );
                break;
        }

        $nextHandler = $this->nextHandler;
        return $nextHandler($command, $request);
    }

    private function endpointPatternDecider(CommandInterface $command)
    {
        $accelerate = isset($command['@use_accelerate_endpoint'])
            ? $command['@use_accelerate_endpoint'] : $this->accelerateByDefault;
        $dualStack = isset($command['@use_dual_stack_endpoint'])
            ? $command['@use_dual_stack_endpoint'] : $this->dualStackByDefault;

        if ($accelerate && $dualStack) {
            // When try to enable both for operations excluded from s3-accelerate,
            // only dualstack endpoints will be enabled.
            return $this->canAccelerate($command)
                ? self::ACCELERATE_DUALSTACK
                : self::DUALSTACK;
        } elseif ($accelerate && $this->canAccelerate($command)) {
            return self::ACCELERATE;
        } elseif ($dualStack) {
            return self::DUALSTACK;
        }
        return self::NO_PATTERN;
    }

    private function canAccelerate(CommandInterface $command)
    {
        return empty(self::$exclusions[$command->getName()])
            && S3Client::isBucketDnsCompatible($command['Bucket']);
    }

    private function applyDualStackEndpoint(RequestInterface $request)
    {
        $request = $request->withUri(
            $request->getUri()
                ->withHost($this->getDualStackHost())
        );
        return $request;
    }

    private function getDualStackHost()
    {
        return "s3.dualstack.{$this->region}.amazonaws.com";
    }

    private function applyAccelerateEndpoint(
        CommandInterface $command,
        RequestInterface $request,
        $pattern
    ) {
        $request = $request->withUri(
            $request->getUri()
                ->withHost($this->getAccelerateHost($command, $pattern))
                ->withPath($this->getBucketlessPath(
                    $request->getUri()->getPath(),
                    $command
                ))
        );
        return $request;
    }

    private function getAccelerateHost(CommandInterface $command, $pattern)
    {
        return "{$command['Bucket']}.{$pattern}.amazonaws.com";
    }

    private function getBucketlessPath($path, CommandInterface $command)
    {
        $pattern = '/^\\/' . preg_quote($command['Bucket'], '/') . '/';
        return preg_replace($pattern, '', $path) ?: '/';
    }
}
