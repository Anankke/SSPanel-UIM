<?php

use Behat\Behat\Context\BehatContext;

class ChargeContext extends BehatContext
{
    public function __construct(array $parameters)
    {
        $this->token = NULL;
        $this->chargeId = NULL;
        $this->cvv = '123';
    }

    /**
     * @Given /^CVV code "([^"]*)"$/
     */
    public function cvvCode($cvvCode)
    {
        $this->cvv = $cvvCode;
    }

    /**
     * @Given /^charge ID "([^"]*)"$/
     */
    public function chargeId($chargeId)
    {
        $this->chargeId = $chargeId;
    }

    /**
    * @When /^test token is retrieved$/
    */
    public function testTokenIsRetrieved()
    {
        $tokenModel = new Paymentwall_OneTimeToken();
        $this->token = $tokenModel->create($this->getTestDetailsForOneTimeToken())->getToken();
        if (strpos($this->token, 'ot_') === FALSE) {
            throw new Exception($this->token->getPublicData());
        }
    }

    /**
    * @Then /^charge should be successful$/
    */
    public function chargeShouldBeSuccessful()
    {
        $charge = $this->getChargeObject();
        if (!$charge->isSuccessful()) {
            throw new Exception($charge->getPublicData());
        }
    }

    /**
    * @Then /^charge should be refunded$/
    */
    public function chargeShouldBeRefunded()
    {
        $chargeToBeRefunded = new Paymentwall_Charge($this->chargeId);
        if (!$chargeToBeRefunded->refund()->isRefunded()) {
            throw new Exception($chargeToBeRefunded->getPublicData());
        }
    }

    /**
    * @Then /^I see this error message "([^"]*)"$/
    */
    public function iSeeThisErrorMessage($errorMessage = '')
    {
        $charge = $this->getChargeObject();
        $errors = json_decode($charge->getPublicData(), TRUE);
        if (strpos($errorMessage, $errors['error']['message']) === FALSE) {
            throw new Exception($charge->getPublicData());
        }
    }

    protected function getChargeObject()
    {
        $chargeModel = new Paymentwall_Charge();
        return $chargeModel->create($this->getTestDetailsForCharge());
    }

    protected function getTestDetailsForCharge()
    {
        return array(
            'token' => $this->token,
            'email' => 'test@user.com',
            'currency' => 'USD',
            'amount' => 9.99,
            'browser_domain' => 'https://www.paymentwall.com',
            'browser_ip' => '72.229.28.185',
            'description' => 'Test Charge'
        );
    }

    protected function getTestDetailsForOneTimeToken()
    {
        return array_merge(
            array('public_key' => Paymentwall_Config::getInstance()->getPublicKey()),
            $this->getTestCardDetails()
        );
    }

    protected function getTestCardDetails()
    {
        return array(
            'card[number]' => '4242424242424242',
            'card[exp_month]' => '11',
            'card[exp_year]' => '19',
            'card[cvv]' => $this->cvv
        );
    }
}