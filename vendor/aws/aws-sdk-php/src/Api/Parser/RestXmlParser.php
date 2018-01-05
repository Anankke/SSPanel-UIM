<?php
namespace Aws\Api\Parser;

use Aws\Api\StructureShape;
use Aws\Api\Service;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal Implements REST-XML parsing (e.g., S3, CloudFront, etc...)
 */
class RestXmlParser extends AbstractRestParser
{
    use PayloadParserTrait;

    /** @var XmlParser */
    private $parser;

    /**
     * @param Service   $api    Service description
     * @param XmlParser $parser XML body parser
     */
    public function __construct(Service $api, XmlParser $parser = null)
    {
        parent::__construct($api);
        $this->parser = $parser ?: new XmlParser();
    }

    protected function payload(
        ResponseInterface $response,
        StructureShape $member,
        array &$result
    ) {
        $xml = $this->parseXml($response->getBody());
        $result += $this->parser->parse($member, $xml);
    }
}
