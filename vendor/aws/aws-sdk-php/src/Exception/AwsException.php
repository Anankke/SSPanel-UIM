<?php
namespace Aws\Exception;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Aws\CommandInterface;
use Aws\ResultInterface;

/**
 * Represents an AWS exception that is thrown when a command fails.
 */
class AwsException extends \RuntimeException
{
    /** @var ResponseInterface */
    private $response;
    private $request;
    private $result;
    private $command;
    private $requestId;
    private $errorType;
    private $errorCode;
    private $connectionError;
    private $transferInfo;

    /**
     * @param string           $message Exception message
     * @param CommandInterface $command
     * @param array            $context Exception context
     * @param \Exception       $previous  Previous exception (if any)
     */
    public function __construct(
        $message,
        CommandInterface $command,
        array $context = [],
        \Exception $previous = null
    ) {
        $this->command = $command;
        $this->response = isset($context['response']) ? $context['response'] : null;
        $this->request = isset($context['request']) ? $context['request'] : null;
        $this->requestId = isset($context['request_id'])
            ? $context['request_id']
            : null;
        $this->errorType = isset($context['type']) ? $context['type'] : null;
        $this->errorCode = isset($context['code']) ? $context['code'] : null;
        $this->connectionError = !empty($context['connection_error']);
        $this->result = isset($context['result']) ? $context['result'] : null;
        $this->transferInfo = isset($context['transfer_stats'])
            ? $context['transfer_stats']
            : [];
        parent::__construct($message, 0, $previous);
    }

    public function __toString()
    {
        if (!$this->getPrevious()) {
            return parent::__toString();
        }

        // PHP strangely shows the innermost exception first before the outer
        // exception message. It also has a default character limit for
        // exception message strings such that the "next" exception (this one)
        // might not even get shown, causing developers to attempt to catch
        // the inner exception instead of the actual exception because they
        // can't see the outer exception's __toString output.
        return sprintf(
            "exception '%s' with message '%s'\n\n%s",
            get_class($this),
            $this->getMessage(),
            parent::__toString()
        );
    }

    /**
     * Get the command that was executed.
     *
     * @return CommandInterface
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Get the sent HTTP request if any.
     *
     * @return RequestInterface|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the received HTTP response if any.
     *
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get the result of the exception if available
     *
     * @return ResultInterface|null
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Returns true if this is a connection error.
     *
     * @return bool
     */
    public function isConnectionError()
    {
        return $this->connectionError;
    }

    /**
     * If available, gets the HTTP status code of the corresponding response
     *
     * @return int|null
     */
    public function getStatusCode()
    {
        return $this->response ? $this->response->getStatusCode() : null;
    }

    /**
     * Get the request ID of the error. This value is only present if a
     * response was received and is not present in the event of a networking
     * error.
     *
     * @return string|null Returns null if no response was received
     */
    public function getAwsRequestId()
    {
        return $this->requestId;
    }

    /**
     * Get the AWS error type.
     *
     * @return string|null Returns null if no response was received
     */
    public function getAwsErrorType()
    {
        return $this->errorType;
    }

    /**
     * Get the AWS error code.
     *
     * @return string|null Returns null if no response was received
     */
    public function getAwsErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Get all transfer information as an associative array if no $name
     * argument is supplied, or gets a specific transfer statistic if
     * a $name attribute is supplied (e.g., 'retries_attempted').
     *
     * @param string $name Name of the transfer stat to retrieve
     *
     * @return mixed|null|array
     */
    public function getTransferInfo($name = null)
    {
        if (!$name) {
            return $this->transferInfo;
        }

        return isset($this->transferInfo[$name])
            ? $this->transferInfo[$name]
            : null;
    }

    /**
     * Replace the transfer information associated with an exception.
     *
     * @param array $info
     */
    public function setTransferInfo(array $info)
    {
        $this->transferInfo = $info;
    }
}
