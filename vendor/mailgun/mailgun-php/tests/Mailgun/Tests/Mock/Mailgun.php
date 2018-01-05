<?PHP

namespace Mailgun\Tests\Mock;

use Mailgun\Mailgun as Base;
use Mailgun\Tests\Mock\Connection\TestBroker;

class Mailgun extends Base
{
    protected $debug;
    protected $restClient;

    public function __construct($apiKey = null, $apiEndpoint = "api.mailgun.net", $apiVersion = "v2")
    {
        $this->restClient = new TestBroker($apiKey, $apiEndpoint, $apiVersion);
    }
}
