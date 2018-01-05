<?php
namespace Aws\Api\Parser;

use Aws\Api\Service;
use Aws\Result;
use Aws\CommandInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal Parses query (XML) responses (e.g., EC2, SQS, and many others)
 */
class QueryParser extends AbstractParser
{
    use PayloadParserTrait;

    /** @var XmlParser */
    private $xmlParser;

    /** @var bool */
    private $honorResultWrapper;

    /**
     * @param Service   $api                Service description
     * @param XmlParser $xmlParser          Optional XML parser
     * @param bool      $honorResultWrapper Set to false to disable the peeling
     *                                      back of result wrappers from the
     *                                      output structure.
     */
    public function __construct(
        Service $api,
        XmlParser $xmlParser = null,
        $honorResultWrapper = true
    ) {
        parent::__construct($api);
        $this->xmlParser = $xmlParser ?: new XmlParser();
        $this->honorResultWrapper = $honorResultWrapper;
    }

    public function __invoke(
        CommandInterface $command,
        ResponseInterface $response
    ) {
        $output = $this->api->getOperation($command->getName())->getOutput();
        $xml = $this->parseXml($response->getBody());

        if ($this->honorResultWrapper && $output['resultWrapper']) {
            $xml = $xml->{$output['resultWrapper']};
        }

        return new Result($this->xmlParser->parse($output, $xml));
    }
}
