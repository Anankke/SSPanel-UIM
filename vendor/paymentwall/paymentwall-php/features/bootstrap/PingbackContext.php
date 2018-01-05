<?php

use Behat\Behat\Context\BehatContext;

class PingbackContext extends BehatContext
{
    public function __construct(array $parameters)
    {
        $this->pingbackParameters = null;
        $this->pingbackIpAddress = null;
    }

	/**
	* @Given /^Pingback GET parameters "([^"]*)"$/
	*/
    public function pingbackGetParameters($parameters)
    {   
        parse_str($parameters, $this->pingbackParameters);
    }

    /**
     * @Given /^Pingback IP address "([^"]*)"$/
     */
    public function pingbackIpAddress($ipAddress)
    {
        $this->pingbackIpAddress = $ipAddress;
    }

    /**
     * @When /^Pingback is constructed$/
     */
    public function pingbackIsConstructed()
    {
        $this->pingback = new Paymentwall_Pingback($this->pingbackParameters, $this->pingbackIpAddress);
    }

    /**
     * @Then /^Pingback validation result should be "([^"]*)"$/
     */
    public function pingbackValidationResultShouldBe($value)
    {
    	$validate = $this->pingback->validate();
        if ($validate !== $value) {
            throw new Exception(
                'Pingback Validation returns ' . var_export($validate, true) . (!$validate ? ("\r\nErrors:" . $this->pingback->getErrorSummary()) : '')
            );
        }
    }

    /**
     * @Given /^Pingback method "([^"]*)" should return "([^"]*)"$/
     */
    public function pingbackMethodShouldReturn($method, $value)
    {
        if ($this->pingback->$method() !== $value) {
        	throw new Exception(
                'Pingback method ' . $method . ' returned ' . var_export($value, true)
            );
        }
    }

    /**
     * @Transform /^(true|false)$/
     */
    public function castStringToBoolean($string)
    {
        return filter_var($string, FILTER_VALIDATE_BOOLEAN);
    }
}