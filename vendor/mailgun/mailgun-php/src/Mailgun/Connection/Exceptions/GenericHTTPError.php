<?php
namespace Mailgun\Connection\Exceptions;

class GenericHTTPError extends \Exception
{
	protected $httpResponseCode;
	protected $httpResponseBody;
	
	public function __construct($message=null, $response_code=null, $response_body=null, $code=0, \Exception $previous=null) {
		parent::__construct($message, $code, $previous);
		
		$this->httpResponseCode = $response_code;
		$this->httpResponseBody = $response_body;
	}
	
	public function getHttpResponseCode() {
		return $this->httpResponseCode;
	}
	
	public function getHttpResponseBody() {
		return $this->httpResponseBody;
	}
}

?>
