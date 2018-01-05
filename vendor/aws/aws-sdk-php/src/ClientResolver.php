<?php
namespace Aws;

use Aws\Api\Validator;
use Aws\Api\ApiProvider;
use Aws\Api\Service;
use Aws\Credentials\Credentials;
use Aws\Credentials\CredentialsInterface;
use Aws\Endpoint\Partition;
use Aws\Endpoint\PartitionEndpointProvider;
use Aws\Endpoint\PartitionProviderInterface;
use Aws\Signature\SignatureProvider;
use Aws\Endpoint\EndpointProvider;
use Aws\Credentials\CredentialProvider;
use GuzzleHttp\Promise;
use InvalidArgumentException as IAE;
use Psr\Http\Message\RequestInterface;

/**
 * @internal Resolves a hash of client arguments to construct a client.
 */
class ClientResolver
{
    /** @var array */
    private $argDefinitions;

    /** @var array Map of types to a corresponding function */
    private static $typeMap = [
        'resource' => 'is_resource',
        'callable' => 'is_callable',
        'int'      => 'is_int',
        'bool'     => 'is_bool',
        'string'   => 'is_string',
        'object'   => 'is_object',
        'array'    => 'is_array',
    ];

    private static $defaultArgs = [
        'service' => [
            'type'     => 'value',
            'valid'    => ['string'],
            'doc'      => 'Name of the service to utilize. This value will be supplied by default when using one of the SDK clients (e.g., Aws\\S3\\S3Client).',
            'required' => true,
            'internal' => true
        ],
        'exception_class' => [
            'type'     => 'value',
            'valid'    => ['string'],
            'doc'      => 'Exception class to create when an error occurs.',
            'default'  => 'Aws\Exception\AwsException',
            'internal' => true
        ],
        'scheme' => [
            'type'     => 'value',
            'valid'    => ['string'],
            'default'  => 'https',
            'doc'      => 'URI scheme to use when connecting connect. The SDK will utilize "https" endpoints (i.e., utilize SSL/TLS connections) by default. You can attempt to connect to a service over an unencrypted "http" endpoint by setting ``scheme`` to "http".',
        ],
        'endpoint' => [
            'type'  => 'value',
            'valid' => ['string'],
            'doc'   => 'The full URI of the webservice. This is only required when connecting to a custom endpoint (e.g., a local version of S3).',
            'fn'    => [__CLASS__, '_apply_endpoint'],
        ],
        'region' => [
            'type'     => 'value',
            'valid'    => ['string'],
            'required' => [__CLASS__, '_missing_region'],
            'doc'      => 'Region to connect to. See http://docs.aws.amazon.com/general/latest/gr/rande.html for a list of available regions.',
        ],
        'version' => [
            'type'     => 'value',
            'valid'    => ['string'],
            'required' => [__CLASS__, '_missing_version'],
            'doc'      => 'The version of the webservice to utilize (e.g., 2006-03-01).',
        ],
        'signature_provider' => [
            'type'    => 'value',
            'valid'   => ['callable'],
            'doc'     => 'A callable that accepts a signature version name (e.g., "v4"), a service name, and region, and  returns a SignatureInterface object or null. This provider is used to create signers utilized by the client. See Aws\\Signature\\SignatureProvider for a list of built-in providers',
            'default' => [__CLASS__, '_default_signature_provider'],
        ],
        'endpoint_provider' => [
            'type'     => 'value',
            'valid'    => ['callable'],
            'fn'       => [__CLASS__, '_apply_endpoint_provider'],
            'doc'      => 'An optional PHP callable that accepts a hash of options including a "service" and "region" key and returns NULL or a hash of endpoint data, of which the "endpoint" key is required. See Aws\\Endpoint\\EndpointProvider for a list of built-in providers.',
            'default' => [__CLASS__, '_default_endpoint_provider'],
        ],
        'api_provider' => [
            'type'     => 'value',
            'valid'    => ['callable'],
            'doc'      => 'An optional PHP callable that accepts a type, service, and version argument, and returns an array of corresponding configuration data. The type value can be one of api, waiter, or paginator.',
            'fn'       => [__CLASS__, '_apply_api_provider'],
            'default'  => [ApiProvider::class, 'defaultProvider'],
        ],
        'signature_version' => [
            'type'    => 'config',
            'valid'   => ['string'],
            'doc'     => 'A string representing a custom signature version to use with a service (e.g., v4). Note that per/operation signature version MAY override this requested signature version.',
            'default' => [__CLASS__, '_default_signature_version'],
        ],
        'signing_name' => [
            'type'    => 'config',
            'valid'   => ['string'],
            'doc'     => 'A string representing a custom service name to be used when calculating a request signature.',
            'default' => [__CLASS__, '_default_signing_name'],
        ],
        'signing_region' => [
            'type'    => 'config',
            'valid'   => ['string'],
            'doc'     => 'A string representing a custom region name to be used when calculating a request signature.',
            'default' => [__CLASS__, '_default_signing_region'],
        ],
        'profile' => [
            'type'  => 'config',
            'valid' => ['string'],
            'doc'   => 'Allows you to specify which profile to use when credentials are created from the AWS credentials file in your HOME directory. This setting overrides the AWS_PROFILE environment variable. Note: Specifying "profile" will cause the "credentials" key to be ignored.',
            'fn'    => [__CLASS__, '_apply_profile'],
        ],
        'credentials' => [
            'type'    => 'value',
            'valid'   => [CredentialsInterface::class, CacheInterface::class, 'array', 'bool', 'callable'],
            'doc'     => 'Specifies the credentials used to sign requests. Provide an Aws\Credentials\CredentialsInterface object, an associative array of "key", "secret", and an optional "token" key, `false` to use null credentials, or a callable credentials provider used to create credentials or return null. See Aws\\Credentials\\CredentialProvider for a list of built-in credentials providers. If no credentials are provided, the SDK will attempt to load them from the environment.',
            'fn'      => [__CLASS__, '_apply_credentials'],
            'default' => [CredentialProvider::class, 'defaultProvider'],
        ],
        'stats' => [
            'type'  => 'value',
            'valid' => ['bool', 'array'],
            'default' => false,
            'doc'   => 'Set to true to gather transfer statistics on requests sent. Alternatively, you can provide an associative array with the following keys: retries: (bool) Set to false to disable reporting on retries attempted; http: (bool) Set to true to enable collecting statistics from lower level HTTP adapters (e.g., values returned in GuzzleHttp\TransferStats). HTTP handlers must support an http_stats_receiver option for this to have an effect; timer: (bool) Set to true to enable a command timer that reports the total wall clock time spent on an operation in seconds.',
            'fn'    => [__CLASS__, '_apply_stats'],
        ],
        'retries' => [
            'type'    => 'value',
            'valid'   => ['int'],
            'doc'     => 'Configures the maximum number of allowed retries for a client (pass 0 to disable retries). ',
            'fn'      => [__CLASS__, '_apply_retries'],
            'default' => 3,
        ],
        'validate' => [
            'type'    => 'value',
            'valid'   => ['bool', 'array'],
            'default' => true,
            'doc'     => 'Set to false to disable client-side parameter validation. Set to true to utilize default validation constraints. Set to an associative array of validation options to enable specific validation constraints.',
            'fn'      => [__CLASS__, '_apply_validate'],
        ],
        'debug' => [
            'type'  => 'value',
            'valid' => ['bool', 'array'],
            'doc'   => 'Set to true to display debug information when sending requests. Alternatively, you can provide an associative array with the following keys: logfn: (callable) Function that is invoked with log messages; stream_size: (int) When the size of a stream is greater than this number, the stream data will not be logged (set to "0" to not log any stream data); scrub_auth: (bool) Set to false to disable the scrubbing of auth data from the logged messages; http: (bool) Set to false to disable the "debug" feature of lower level HTTP adapters (e.g., verbose curl output).',
            'fn'    => [__CLASS__, '_apply_debug'],
        ],
        'http' => [
            'type'    => 'value',
            'valid'   => ['array'],
            'default' => [],
            'doc'     => 'Set to an array of SDK request options to apply to each request (e.g., proxy, verify, etc.).',
        ],
        'http_handler' => [
            'type'    => 'value',
            'valid'   => ['callable'],
            'doc'     => 'An HTTP handler is a function that accepts a PSR-7 request object and returns a promise that is fulfilled with a PSR-7 response object or rejected with an array of exception data. NOTE: This option supersedes any provided "handler" option.',
            'fn'      => [__CLASS__, '_apply_http_handler']
        ],
        'handler' => [
            'type'     => 'value',
            'valid'    => ['callable'],
            'doc'      => 'A handler that accepts a command object, request object and returns a promise that is fulfilled with an Aws\ResultInterface object or rejected with an Aws\Exception\AwsException. A handler does not accept a next handler as it is terminal and expected to fulfill a command. If no handler is provided, a default Guzzle handler will be utilized.',
            'fn'       => [__CLASS__, '_apply_handler'],
            'default'  => [__CLASS__, '_default_handler']
        ],
        'ua_append' => [
            'type'     => 'value',
            'valid'    => ['string', 'array'],
            'doc'      => 'Provide a string or array of strings to send in the User-Agent header.',
            'fn'       => [__CLASS__, '_apply_user_agent'],
            'default'  => [],
        ],
    ];

    /**
     * Gets an array of default client arguments, each argument containing a
     * hash of the following:
     *
     * - type: (string, required) option type described as follows:
     *   - value: The default option type.
     *   - config: The provided value is made available in the client's
     *     getConfig() method.
     * - valid: (array, required) Valid PHP types or class names. Note: null
     *   is not an allowed type.
     * - required: (bool, callable) Whether or not the argument is required.
     *   Provide a function that accepts an array of arguments and returns a
     *   string to provide a custom error message.
     * - default: (mixed) The default value of the argument if not provided. If
     *   a function is provided, then it will be invoked to provide a default
     *   value. The function is provided the array of options and is expected
     *   to return the default value of the option.
     * - doc: (string) The argument documentation string.
     * - fn: (callable) Function used to apply the argument. The function
     *   accepts the provided value, array of arguments by reference, and an
     *   event emitter.
     *
     * Note: Order is honored and important when applying arguments.
     *
     * @return array
     */
    public static function getDefaultArguments()
    {
        return self::$defaultArgs;
    }

    /**
     * @param array $argDefinitions Client arguments.
     */
    public function __construct(array $argDefinitions)
    {
        $this->argDefinitions = $argDefinitions;
    }

    /**
     * Resolves client configuration options and attached event listeners.
     *
     * @param array       $args Provided constructor arguments.
     * @param HandlerList $list Handler list to augment.
     *
     * @return array Returns the array of provided options.
     * @throws \InvalidArgumentException
     * @see Aws\AwsClient::__construct for a list of available options.
     */
    public function resolve(array $args, HandlerList $list)
    {
        $args['config'] = [];
        foreach ($this->argDefinitions as $key => $a) {
            // Add defaults, validate required values, and skip if not set.
            if (!isset($args[$key])) {
                if (isset($a['default'])) {
                    // Merge defaults in when not present.
                    $args[$key] = is_callable($a['default'])
                        ? $a['default']($args)
                        : $a['default'];
                } elseif (empty($a['required'])) {
                    continue;
                } else {
                    $this->throwRequired($args);
                }
            }

            // Validate the types against the provided value.
            foreach ($a['valid'] as $check) {
                if (isset(self::$typeMap[$check])) {
                    $fn = self::$typeMap[$check];
                    if ($fn($args[$key])) {
                        goto is_valid;
                    }
                } elseif ($args[$key] instanceof $check) {
                    goto is_valid;
                }
            }

            $this->invalidType($key, $args[$key]);

            // Apply the value
            is_valid:
            if (isset($a['fn'])) {
                $a['fn']($args[$key], $args, $list);
            }

            if ($a['type'] === 'config') {
                $args['config'][$key] = $args[$key];
            }
        }

        return $args;
    }

    /**
     * Creates a verbose error message for an invalid argument.
     *
     * @param string $name        Name of the argument that is missing.
     * @param array  $args        Provided arguments
     * @param bool   $useRequired Set to true to show the required fn text if
     *                            available instead of the documentation.
     * @return string
     */
    private function getArgMessage($name, $args = [], $useRequired = false)
    {
        $arg = $this->argDefinitions[$name];
        $msg = '';
        $modifiers = [];
        if (isset($arg['valid'])) {
            $modifiers[] = implode('|', $arg['valid']);
        }
        if (isset($arg['choice'])) {
            $modifiers[] = 'One of ' . implode(', ', $arg['choice']);
        }
        if ($modifiers) {
            $msg .= '(' . implode('; ', $modifiers) . ')';
        }
        $msg = wordwrap("{$name}: {$msg}", 75, "\n  ");

        if ($useRequired && is_callable($arg['required'])) {
            $msg .= "\n\n  ";
            $msg .= str_replace("\n", "\n  ", call_user_func($arg['required'], $args));
        } elseif (isset($arg['doc'])) {
            $msg .= wordwrap("\n\n  {$arg['doc']}", 75, "\n  ");
        }

        return $msg;
    }

    /**
     * Throw when an invalid type is encountered.
     *
     * @param string $name     Name of the value being validated.
     * @param mixed  $provided The provided value.
     * @throws \InvalidArgumentException
     */
    private function invalidType($name, $provided)
    {
        $expected = implode('|', $this->argDefinitions[$name]['valid']);
        $msg = "Invalid configuration value "
            . "provided for \"{$name}\". Expected {$expected}, but got "
            . describe_type($provided) . "\n\n"
            . $this->getArgMessage($name);
        throw new IAE($msg);
    }

    /**
     * Throws an exception for missing required arguments.
     *
     * @param array $args Passed in arguments.
     * @throws \InvalidArgumentException
     */
    private function throwRequired(array $args)
    {
        $missing = [];
        foreach ($this->argDefinitions as $k => $a) {
            if (empty($a['required'])
                || isset($a['default'])
                || array_key_exists($k, $args)
            ) {
                continue;
            }
            $missing[] = $this->getArgMessage($k, $args, true);
        }
        $msg = "Missing required client configuration options: \n\n";
        $msg .= implode("\n\n", $missing);
        throw new IAE($msg);
    }

    public static function _apply_retries($value, array &$args, HandlerList $list)
    {
        if ($value) {
            $decider = RetryMiddleware::createDefaultDecider($value);
            $list->appendSign(
                Middleware::retry($decider, null, $args['stats']['retries']),
                'retry'
            );
        }
    }

    public static function _apply_credentials($value, array &$args)
    {
        if (is_callable($value)) {
            return;
        } elseif ($value instanceof CredentialsInterface) {
            $args['credentials'] = CredentialProvider::fromCredentials($value);
        } elseif (is_array($value)
            && isset($value['key'])
            && isset($value['secret'])
        ) {
            $args['credentials'] = CredentialProvider::fromCredentials(
                new Credentials(
                    $value['key'],
                    $value['secret'],
                    isset($value['token']) ? $value['token'] : null,
                    isset($value['expires']) ? $value['expires'] : null
                )
            );
        } elseif ($value === false) {
            $args['credentials'] = CredentialProvider::fromCredentials(
                new Credentials('', '')
            );
            $args['config']['signature_version'] = 'anonymous';
        } elseif ($value instanceof CacheInterface) {
            $args['credentials'] = CredentialProvider::defaultProvider($args);
        } else {
            throw new IAE('Credentials must be an instance of '
                . 'Aws\Credentials\CredentialsInterface, an associative '
                . 'array that contains "key", "secret", and an optional "token" '
                . 'key-value pairs, a credentials provider function, or false.');
        }
    }

    public static function _apply_api_provider(callable $value, array &$args, HandlerList $list)
    {
        $api = new Service(
            ApiProvider::resolve(
                $value,
                'api',
                $args['service'],
                $args['version']
            ),
            $value
        );
        $args['api'] = $api;
        $args['serializer'] = Service::createSerializer($api, $args['endpoint']);
        $args['parser'] = Service::createParser($api);
        $args['error_parser'] = Service::createErrorParser($api->getProtocol());
        $list->prependBuild(Middleware::requestBuilder($args['serializer']), 'builder');
    }

    public static function _apply_endpoint_provider(callable $value, array &$args)
    {
        if (!isset($args['endpoint'])) {
            // Invoke the endpoint provider and throw if it does not resolve.
            $result = EndpointProvider::resolve($value, [
                'service' => $args['service'],
                'region'  => $args['region'],
                'scheme'  => $args['scheme']
            ]);

            $args['endpoint'] = $result['endpoint'];

            if (isset($result['signatureVersion'])) {
                $args['config']['signature_version'] = $result['signatureVersion'];
            }
            if (isset($result['signingRegion'])) {
                $args['config']['signing_region'] = $result['signingRegion'];
            }
            if (isset($result['signingName'])) {
                $args['config']['signing_name'] = $result['signingName'];
            }
        }
    }

    public static function _apply_debug($value, array &$args, HandlerList $list)
    {
        if ($value !== false) {
            $list->interpose(new TraceMiddleware($value === true ? [] : $value));
        }
    }

    public static function _apply_stats($value, array &$args, HandlerList $list)
    {
        // Create an array of stat collectors that are disabled (set to false)
        // by default. If the user has passed in true, enable all stat
        // collectors.
        $defaults = array_fill_keys(
            ['http', 'retries', 'timer'],
            $value === true
        );
        $args['stats'] = is_array($value)
            ? array_replace($defaults, $value)
            : $defaults;

        if ($args['stats']['timer']) {
            $list->prependInit(Middleware::timer(), 'timer');
        }
    }

    public static function _apply_profile($_, array &$args)
    {
        $args['credentials'] = CredentialProvider::ini($args['profile']);
    }

    public static function _apply_validate($value, array &$args, HandlerList $list)
    {
        if ($value === false) {
            return;
        }

        $validator = $value === true
            ? new Validator()
            : new Validator($value);
        $list->appendValidate(
            Middleware::validation($args['api'], $validator),
            'validation'
        );
    }

    public static function _apply_handler($value, array &$args, HandlerList $list)
    {
        $list->setHandler($value);
    }

    public static function _default_handler(array &$args)
    {
        return new WrappedHttpHandler(
            default_http_handler(),
            $args['parser'],
            $args['error_parser'],
            $args['exception_class'],
            $args['stats']['http']
        );
    }

    public static function _apply_http_handler($value, array &$args, HandlerList $list)
    {
        $args['handler'] = new WrappedHttpHandler(
            $value,
            $args['parser'],
            $args['error_parser'],
            $args['exception_class'],
            $args['stats']['http']
        );
    }

    public static function _apply_user_agent($value, array &$args, HandlerList $list)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $value = array_map('strval', $value);

        array_unshift($value, 'aws-sdk-php/' . Sdk::VERSION);
        $args['ua_append'] = $value;

        $list->appendBuild(static function (callable $handler) use ($value) {
            return function (
                CommandInterface $command,
                RequestInterface $request
            ) use ($handler, $value) {
                return $handler($command, $request->withHeader(
                    'User-Agent',
                    implode(' ', array_merge(
                        $value,
                        $request->getHeader('User-Agent')
                    ))
                ));
            };
        });
    }

    public static function _apply_endpoint($value, array &$args, HandlerList $list)
    {
        $parts = parse_url($value);
        if (empty($parts['scheme']) || empty($parts['host'])) {
            throw new IAE(
                'Endpoints must be full URIs and include a scheme and host'
            );
        }

        $args['endpoint'] = $value;
    }

    public static function _default_endpoint_provider(array $args)
    {
        return PartitionEndpointProvider::defaultProvider()
            ->getPartition($args['region'], $args['service']);
    }

    public static function _default_signature_provider()
    {
        return SignatureProvider::defaultProvider();
    }

    public static function _default_signature_version(array &$args)
    {
        if (isset($args['config']['signature_version'])) {
            return $args['config']['signature_version'];
        }

        $args['__partition_result'] = isset($args['__partition_result'])
            ? isset($args['__partition_result'])
            : call_user_func(PartitionEndpointProvider::defaultProvider(), [
                'service' => $args['service'],
                'region' => $args['region'],
            ]);

        return isset($args['__partition_result']['signatureVersion'])
            ? $args['__partition_result']['signatureVersion']
            : $args['api']->getSignatureVersion();
    }

    public static function _default_signing_name(array &$args)
    {
        if (isset($args['config']['signing_name'])) {
            return $args['config']['signing_name'];
        }

        $args['__partition_result'] = isset($args['__partition_result'])
            ? isset($args['__partition_result'])
            : call_user_func(PartitionEndpointProvider::defaultProvider(), [
                'service' => $args['service'],
                'region' => $args['region'],
            ]);

        if (isset($args['__partition_result']['signingName'])) {
            return $args['__partition_result']['signingName'];
        }

        if ($signingName = $args['api']->getSigningName()) {
            return $signingName;
        }

        return $args['service'];
    }

    public static function _default_signing_region(array &$args)
    {
        if (isset($args['config']['signing_region'])) {
            return $args['config']['signing_region'];
        }

        $args['__partition_result'] = isset($args['__partition_result'])
            ? isset($args['__partition_result'])
            : call_user_func(PartitionEndpointProvider::defaultProvider(), [
                'service' => $args['service'],
                'region' => $args['region'],
            ]);

        return isset($args['__partition_result']['signingRegion'])
            ? $args['__partition_result']['signingRegion']
            : $args['region'];
    }

    public static function _missing_version(array $args)
    {
        $service = isset($args['service']) ? $args['service'] : '';
        $versions = ApiProvider::defaultProvider()->getVersions($service);
        $versions = implode("\n", array_map(function ($v) {
            return "* \"$v\"";
        }, $versions)) ?: '* (none found)';

        return <<<EOT
A "version" configuration value is required. Specifying a version constraint
ensures that your code will not be affected by a breaking change made to the
service. For example, when using Amazon S3, you can lock your API version to
"2006-03-01".

Your build of the SDK has the following version(s) of "{$service}": {$versions}

You may provide "latest" to the "version" configuration value to utilize the
most recent available API version that your client's API provider can find.
Note: Using 'latest' in a production application is not recommended.

A list of available API versions can be found on each client's API documentation
page: http://docs.aws.amazon.com/aws-sdk-php/v3/api/index.html. If you are
unable to load a specific API version, then you may need to update your copy of
the SDK.
EOT;
    }

    public static function _missing_region(array $args)
    {
        $service = isset($args['service']) ? $args['service'] : '';

        return <<<EOT
A "region" configuration value is required for the "{$service}" service
(e.g., "us-west-2"). A list of available public regions and endpoints can be
found at http://docs.aws.amazon.com/general/latest/gr/rande.html.
EOT;
    }
}
