<?PHP
namespace Mailgun\Tests\Messages;

use Mailgun\Tests\Mock\Mailgun;

class BatchMessageTest extends \Mailgun\Tests\MailgunTestCase
{

    private $client;
    private $sampleDomain = "samples.mailgun.org";

    public function setUp()
    {
        $this->client = new Mailgun("My-Super-Awesome-API-Key");
    }

    public function testBlankInstantiation()
    {
        $message = $this->client->BatchMessage($this->sampleDomain);
        $this->assertTrue(is_array($message->getMessage()));
    }

    public function testAddRecipient()
    {
        $message = $this->client->BatchMessage($this->sampleDomain);
        $message->addToRecipient("test@samples.mailgun.org", array("first" => "Test", "last" => "User"));
        $messageObj = $message->getMessage();
        $this->assertEquals(array("to" => array("'Test User' <test@samples.mailgun.org>")), $messageObj);

        $reflectionClass = new \ReflectionClass(get_class($message));
        $property        = $reflectionClass->getProperty('counters');
        $property->setAccessible(true);
        $array = $property->getValue($message);
        $this->assertEquals(1, $array['recipients']['to']);
    }

    public function testRecipientVariablesOnTo()
    {
        $message = $this->client->BatchMessage($this->sampleDomain);
        $message->addToRecipient("test@samples.mailgun.org", array("first" => "Test", "last" => "User"));
        $messageObj = $message->getMessage();
        $this->assertEquals(array("to" => array("'Test User' <test@samples.mailgun.org>")), $messageObj);

        $reflectionClass = new \ReflectionClass(get_class($message));
        $property        = $reflectionClass->getProperty('batchRecipientAttributes');
        $property->setAccessible(true);
        $propertyValue = $property->getValue($message);
        $this->assertEquals("Test", $propertyValue['test@samples.mailgun.org']['first']);
        $this->assertEquals("User", $propertyValue['test@samples.mailgun.org']['last']);
    }

    public function testRecipientVariablesOnCc()
    {
        $message = $this->client->BatchMessage($this->sampleDomain);
        $message->addCcRecipient("test@samples.mailgun.org", array("first" => "Test", "last" => "User"));
        $messageObj = $message->getMessage();
        $this->assertEquals(array("cc" => array("'Test User' <test@samples.mailgun.org>")), $messageObj);

        $reflectionClass = new \ReflectionClass(get_class($message));
        $property        = $reflectionClass->getProperty('batchRecipientAttributes');
        $property->setAccessible(true);
        $propertyValue = $property->getValue($message);
        $this->assertEquals("Test", $propertyValue['test@samples.mailgun.org']['first']);
        $this->assertEquals("User", $propertyValue['test@samples.mailgun.org']['last']);
    }

    public function testRecipientVariablesOnBcc()
    {
        $message = $this->client->BatchMessage($this->sampleDomain);
        $message->addBccRecipient("test@samples.mailgun.org", array("first" => "Test", "last" => "User"));
        $messageObj = $message->getMessage();
        $this->assertEquals(array("bcc" => array("'Test User' <test@samples.mailgun.org>")), $messageObj);

        $reflectionClass = new \ReflectionClass(get_class($message));
        $property        = $reflectionClass->getProperty('batchRecipientAttributes');
        $property->setAccessible(true);
        $propertyValue = $property->getValue($message);
        $this->assertEquals("Test", $propertyValue['test@samples.mailgun.org']['first']);
        $this->assertEquals("User", $propertyValue['test@samples.mailgun.org']['last']);
    }

    public function testAddMultipleBatchRecipients()
    {
        $message = $this->client->BatchMessage($this->sampleDomain);
        for ($i = 0; $i < 100; $i++) {
            $message->addToRecipient("$i@samples.mailgun.org", array("first" => "Test", "last" => "User $i"));
        }
        $messageObj = $message->getMessage();
        $this->assertEquals(100, count($messageObj["to"]));
    }

    public function testMaximumBatchSize()
    {
        $message = $this->client->BatchMessage($this->sampleDomain);
        $message->setFromAddress("samples@mailgun.org", array("first" => "Test", "last" => "User"));
        $message->setSubject("This is the subject of the message!");
        $message->setTextBody("This is the text body of the message!");
        for ($i = 0; $i < 1001; $i++) {
            $message->addToRecipient("$i@samples.mailgun.org", array("first" => "Test", "last" => "User $i"));
        }
        $messageObj = $message->getMessage();
        $this->assertEquals(1, count($messageObj["to"]));
    }

    public function testAttributeResetOnEndBatchMessage()
    {
        $message = $this->client->BatchMessage($this->sampleDomain);
        $message->addToRecipient("test-user@samples.mailgun.org", array("first" => "Test", "last" => "User"));
        $message->setFromAddress("samples@mailgun.org", array("first" => "Test", "last" => "User"));
        $message->setSubject("This is the subject of the message!");
        $message->setTextBody("This is the text body of the message!");
        $message->finalize();
        $messageObj = $message->getMessage();
        $this->assertTrue(true, empty($messageObj));
    }

    public function testDefaultIDInVariables()
    {
        $message = $this->client->BatchMessage($this->sampleDomain);
        $message->addToRecipient("test-user@samples.mailgun.org", array("first" => "Test", "last" => "User"));

        $reflectionClass = new \ReflectionClass(get_class($message));
        $property        = $reflectionClass->getProperty('batchRecipientAttributes');
        $property->setAccessible(true);
        $propertyValue = $property->getValue($message);
        $this->assertEquals(1, $propertyValue['test-user@samples.mailgun.org']['id']);
    }

    public function testgetMessageIds()
    {
        $message = $this->client->BatchMessage($this->sampleDomain);
        $message->addToRecipient("test-user@samples.mailgun.org", array("first" => "Test", "last" => "User"));
        $message->setFromAddress("samples@mailgun.org", array("first" => "Test", "last" => "User"));
        $message->setSubject("This is the subject of the message!");
        $message->setTextBody("This is the text body of the message!");
        $message->finalize();

        $this->assertEquals(array("1234"), $message->getMessageIds());
    }

    public function testInvalidMissingRequiredMIMEParametersExceptionGetsFlungNoFrom()
    {
        $this->setExpectedException("\\Mailgun\\Messages\\Exceptions\\MissingRequiredMIMEParameters");

        $message = $this->client->BatchMessage($this->sampleDomain);
        $message->sendMessage(array(1, 2, 3));
    }

    public function testInvalidMissingRequiredMIMEParametersExceptionGetsFlungNoTo()
    {
        $this->setExpectedException("\\Mailgun\\Messages\\Exceptions\\MissingRequiredMIMEParameters");

        $message = $this->client->BatchMessage($this->sampleDomain);
        $message->sendMessage(array("from" => 1, 2, 3));
    }

    public function testInvalidMissingRequiredMIMEParametersExceptionGetsFlungNoSubject()
    {
        $this->setExpectedException("\\Mailgun\\Messages\\Exceptions\\MissingRequiredMIMEParameters");

        $message = $this->client->BatchMessage($this->sampleDomain);
        $message->sendMessage(array("from" => 1, "to" => 2, 3));
    }

    public function testInvalidMissingRequiredMIMEParametersExceptionGetsFlungNoTextOrHtml()
    {
        $this->setExpectedException("\\Mailgun\\Messages\\Exceptions\\MissingRequiredMIMEParameters");

        $message = $this->client->BatchMessage($this->sampleDomain);
        $message->sendMessage(array("from" => 1, "to" => 2, "subject" => 3));
    }
}
