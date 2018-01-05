<?php
namespace Aws;

use Aws\Exception\AwsException;
use Exception;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7;

/**
 * @internal Middleware that retries failures.
 */
class RetryMiddleware
{
    private static $retryStatusCodes = [
        500 => true,
        502 => true,
        503 => true,
        504 => true
    ];

    private static $retryCodes = [
        // Throttling error
        'RequestLimitExceeded'                   => true,
        'Throttling'                             => true,
        'ThrottlingException'                    => true,
        'ProvisionedThroughputExceededException' => true,
        'RequestThrottled'                       => true,
        'BandwidthLimitExceeded'                 => true,
    ];

    private $decider;
    private $delay;
    private $nextHandler;
    private $collectStats;

    public function __construct(
        callable $decider,
        callable $delay,
        callable $nextHandler,
        $collectStats = false
    ) {
        $this->decider = $decider;
        $this->delay = $delay;
        $this->nextHandler = $nextHandler;
        $this->collectStats = (bool) $collectStats;
    }

    /**
     * Creates a default AWS retry decider function.
     *
     * @param int $maxRetries
     *
     * @return callable
     */
    public static function createDefaultDecider($maxRetries = 3)
    {
        return function (
            $retries,
            CommandInterface $command,
            RequestInterface $request,
            ResultInterface $result = null,
            $error = null
        ) use ($maxRetries) {
            // Allow command-level options to override this value
            $maxRetries = null !== $command['@retries'] ?
                $command['@retries']
                : $maxRetries;

            if ($retries >= $maxRetries) {
                return false;
            } elseif (!$error) {
                return isset(self::$retryStatusCodes[$result['@metadata']['statusCode']]);
            } elseif (!($error instanceof AwsException)) {
                return false;
            } elseif ($error->isConnectionError()) {
                return true;
            } elseif (isset(self::$retryCodes[$error->getAwsErrorCode()])) {
                return true;
            } elseif (isset(self::$retryStatusCodes[$error->getStatusCode()])) {
                return true;
            } else {
                return false;
            }
        };
    }

    /**
     * Delay function that calculates an exponential delay.
     *
     * Exponential backoff with jitter, 100ms base, 20 sec ceiling
     *
     * @param $retries
     *
     * @return int
     */
    public static function exponentialDelay($retries)
    {
        return mt_rand(0, (int) min(20000, (int) pow(2, $retries - 1) * 100));
    }

    /**
     * @param CommandInterface $command
     * @param RequestInterface $request
     *
     * @return PromiseInterface
     */
    public function __invoke(
        CommandInterface $command,
        RequestInterface $request = null
    ) {
        $retries = 0;
        $requestStats = [];
        $handler = $this->nextHandler;
        $decider = $this->decider;
        $delay = $this->delay;

        $request = $this->addRetryHeader($request, 0, 0);

        $g = function ($value) use (
            $handler,
            $decider,
            $delay,
            $command,
            $request,
            &$retries,
            &$requestStats,
            &$g
        ) {
            $this->updateHttpStats($value, $requestStats);

            if ($value instanceof \Exception || $value instanceof \Throwable) {
                if (!$decider($retries, $command, $request, null, $value)) {
                    return \GuzzleHttp\Promise\rejection_for(
                        $this->bindStatsToReturn($value, $requestStats)
                    );
                }
            } elseif ($value instanceof ResultInterface
                && !$decider($retries, $command, $request, $value, null)
            ) {
                return $this->bindStatsToReturn($value, $requestStats);
            }

            // Delay fn is called with 0, 1, ... so increment after the call.
            $delayBy = $delay($retries++);
            $command['@http']['delay'] = $delayBy;
            if ($this->collectStats) {
                $this->updateStats($retries, $delayBy, $requestStats);
            }

            // Update retry header with retry count and delayBy
            $request = $this->addRetryHeader($request, $retries, $delayBy);

            return $handler($command, $request)->then($g, $g);
        };

        return $handler($command, $request)->then($g, $g);
    }

    private function addRetryHeader($request, $retries, $delayBy)
    {
        return $request->withHeader('aws-sdk-retry', "{$retries}/{$delayBy}");
    }

    private function updateStats($retries, $delay, array &$stats)
    {
        if (!isset($stats['total_retry_delay'])) {
            $stats['total_retry_delay'] = 0;
        }

        $stats['total_retry_delay'] += $delay;
        $stats['retries_attempted'] = $retries;
    }

    private function updateHttpStats($value, array &$stats)
    {
        if (empty($stats['http'])) {
            $stats['http'] = [];
        }

        if ($value instanceof AwsException) {
            $resultStats = isset($value->getTransferInfo('http')[0])
                ? $value->getTransferInfo('http')[0]
                : [];
            $stats['http'] []= $resultStats;
        } elseif ($value instanceof ResultInterface) {
            $resultStats = isset($value['@metadata']['transferStats']['http'][0])
                ? $value['@metadata']['transferStats']['http'][0]
                : [];
            $stats['http'] []= $resultStats;
        }
    }

    private function bindStatsToReturn($return, array $stats)
    {
        if ($return instanceof ResultInterface) {
            if (!isset($return['@metadata'])) {
                $return['@metadata'] = [];
            }

            $return['@metadata']['transferStats'] = $stats;
        } elseif ($return instanceof AwsException) {
            $return->setTransferInfo($stats);
        }

        return $return;
    }
}
