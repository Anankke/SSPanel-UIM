<?PHP
namespace Mailgun\Tests\Lists;

use Mailgun\Mailgun;

class MailgunTest extends \Mailgun\Tests\MailgunTestCase
{

    public function testSendMessageMissingRequiredMIMEParametersExceptionGetsFlung()
    {
        $this->setExpectedException("\\Mailgun\\Messages\\Exceptions\\MissingRequiredMIMEParameters");

        $client = new Mailgun();
        $client->sendMessage("test.mailgun.com", "etss", 1);
    }

    public function testVerifyWebhookGood() {
        $client = new Mailgun('key-3ax6xnjp29jd6fds4gc373sgvjxteol0');
        $postData = [
            'timestamp' => '1403645220',
            'token' => '5egbgr1vjgqxtrnp65xfznchgdccwh5d6i09vijqi3whgowmn6',
            'signature' => '9cfc5c41582e51246e73c88d34db3af0a3a2692a76fbab81492842f000256d33',
        ];
        assert($client->verifyWebhookSignature($postData));
    }

    public function testVerifyWebhookBad() {
        $client = new Mailgun('key-3ax6xnjp29jd6fds4gc373sgvjxteol0');
        $postData = [
            'timestamp' => '1403645220',
            'token' => 'owyldpe6nxhmrn78epljl6bj0orrki1u3d2v5e6cnlmmuox8jr',
            'signature' => '9cfc5c41582e51246e73c88d34db3af0a3a2692a76fbab81492842f000256d33',
        ];
        assert(!$client->verifyWebhookSignature($postData));
    }
}
