<?php
namespace Mailgun\Tests\Mock\Connection;

use Mailgun\Connection\RestClient;

class TestBroker extends RestClient
{
    private $apiKey;

    protected $apiEndpoint;

    public function __construct($apiKey = null, $apiEndpoint = "api.mailgun.net", $apiVersion = "v2")
    {
        $this->apiKey      = $apiKey;
        $this->apiEndpoint = $apiEndpoint;
    }

    public function post($endpointUrl, $postData = array(), $files = array())
    {
        return $this->responseHandler($endpointUrl, $httpResponseCode = 200);
    }

    public function get($endpointUrl, $queryString = array())
    {
        return $this->responseHandler($endpointUrl, $httpResponseCode = 200);
    }

    public function delete($endpointUrl)
    {
        return $this->responseHandler($endpointUrl, $httpResponseCode = 200);
    }

    public function put($endpointUrl, $queryString)
    {
        return $this->responseHandler($endpointUrl, $httpResponseCode = 200);
    }

    public function responseHandler($endpointUrl, $httpResponseCode = 200)
    {
        if ($httpResponseCode === 200) {
            $result                     = new \stdClass();
            $result->http_response_body = new \stdClass();
            $jsonResponseData           = json_decode('{"message": "Some JSON Response Data", "id": "1234"}');
            foreach ($jsonResponseData as $key => $value) {
                $result->http_response_body->$key = $value;
            }
        } elseif ($httpStatusCode == 400) {
            throw new MissingRequiredMIMEParameters(EXCEPTION_MISSING_REQUIRED_MIME_PARAMETERS);
        } elseif ($httpStatusCode == 401) {
            throw new InvalidCredentials(EXCEPTION_INVALID_CREDENTIALS);
        } elseif ($httpStatusCode == 401) {
            throw new GenericHTTPError(EXCEPTION_INVALID_CREDENTIALS);
        } elseif ($httpStatusCode == 404) {
            throw new MissingEndpoint(EXCEPTION_MISSING_ENDPOINT);
        } else {
            throw new GenericHTTPError(EXCEPTION_GENERIC_HTTP_ERROR);

            return false;
        }
        $result->http_response_code = $httpResponseCode;
        $result->http_endpoint_url  = $endpointUrl;

        return $result;
    }


}
