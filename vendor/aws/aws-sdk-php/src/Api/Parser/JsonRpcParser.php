<?php
namespace Aws\Api\Parser;

use Aws\Api\Service;
use Aws\Result;
use Aws\CommandInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal Implements JSON-RPC parsing (e.g., DynamoDB)
 */
class JsonRpcParser extends AbstractParser
{
    use PayloadParserTrait;

    private $parser;

    /**
     * @param Service    $api    Service description
     * @param JsonParser $parser JSON body builder
     */
    public function __construct(Service $api, JsonParser $parser = null)
    {
        parent::__construct($api);
        $this->parser = $parser ?: new JsonParser();
    }

    public function __invoke(
        CommandInterface $command,
        ResponseInterface $response
    ) {
        $operation = $this->api->getOperation($command->getName());
        $result = null === $operation['output']
            ? null
            : $this->parser->parse(
                $operation->getOutput(),
                $this->parseJson($response->getBody())
            );

        return new Result($result ?: []);
    }
}
