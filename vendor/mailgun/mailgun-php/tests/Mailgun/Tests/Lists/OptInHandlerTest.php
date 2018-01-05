<?PHP
namespace Mailgun\Tests\Lists;

use Mailgun\Tests\Mock\Mailgun;

class OptInHandler extends \Mailgun\Tests\MailgunTestCase
{

    private $client;
    private $sampleDomain = "samples.mailgun.org";
    private $optInHandler;

    public function setUp()
    {
        $this->client = new Mailgun("My-Super-Awesome-API-Key");
        $this->optInHandler = $this->client->OptInHandler();
    }

    public function testReturnOfGenerateHash()
    {
        $generatedHash = $this->optInHandler->generateHash(
            'mytestlist@example.com',
            'mysupersecretappid',
            'testrecipient@example.com'
        );
        $knownHash     = "eyJoIjoiMTllODc2YWNkMWRmNzk4NTc0ZTU0YzhjMzIzOTNiYTNjNzdhNGMxOCIsInAiOiJleUp5SWpvaWRHVnpkSEpsWTJsd2FXVnVkRUJsZUdGdGNHeGxMbU52YlNJc0ltd2lPaUp0ZVhSbGMzUnNhWE4wUUdWNFlXMXdiR1V1WTI5dEluMD0ifQ%3D%3D";
        $this->assertEquals($generatedHash, $knownHash);
    }

    public function testGoodHash()
    {
        $validation = $this->optInHandler->validateHash(
            'mysupersecretappid',
            'eyJoIjoiMTllODc2YWNkMWRmNzk4NTc0ZTU0YzhjMzIzOTNiYTNjNzdhNGMxOCIsInAiOiJleUp5SWpvaWRHVnpkSEpsWTJsd2FXVnVkRUJsZUdGdGNHeGxMbU52YlNJc0ltd2lPaUp0ZVhSbGMzUnNhWE4wUUdWNFlXMXdiR1V1WTI5dEluMD0ifQ%3D%3D'
        );
        $this->assertArrayHasKey('recipientAddress', $validation);
        $this->assertArrayHasKey('mailingList', $validation);
    }

    public function testBadHash()
    {
        $validation = $this->optInHandler->validateHash(
            'mybadsecretappid',
            'eyJoIjoiMTllODc2YWNkMWRmNzk4NTc0ZTU0YzhjMzIzOTNiYTNjNzdhNGMxOCIsInAiOiJleUp5SWpvaWRHVnpkSEpsWTJsd2FXVnVkRUJsZUdGdGNHeGxMbU52YlNJc0ltd2lPaUp0ZVhSbGMzUnNhWE4wUUdWNFlXMXdiR1V1WTI5dEluMD0ifQ%3D%3D'
        );
        $this->assertFalse($validation);
    }
}
