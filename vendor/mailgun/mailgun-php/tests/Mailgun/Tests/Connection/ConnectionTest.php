<?PHP
namespace Mailgun\Tests\Connection;

use Mailgun\Tests\Mock\Mailgun;

class ConnectionTest extends \Mailgun\Tests\MailgunTestCase
{

    private $client;

    public function setUp()
    {
    }

    public function testNewClientInstantiation()
    {
        $this->client = new Mailgun("My-Super-Awesome-API-Key", "samples.mailgun.org", false);
    }
}
