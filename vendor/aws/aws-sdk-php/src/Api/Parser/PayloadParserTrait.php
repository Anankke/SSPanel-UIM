<?php
namespace Aws\Api\Parser;

use Aws\Api\Parser\Exception\ParserException;

trait PayloadParserTrait
{
    /**
     * @param string $json
     *
     * @throws ParserException
     *
     * @return array
     */
    private function parseJson($json)
    {
        $jsonPayload = json_decode($json, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new ParserException('Error parsing JSON: '
                . json_last_error_msg());
        }

        return $jsonPayload;
    }

    /**
     * @param string $xml
     *
     * @throws ParserException
     *
     * @return \SimpleXMLElement
     */
    private function parseXml($xml)
    {
        $priorSetting = libxml_use_internal_errors(true);
        try {
            libxml_clear_errors();
            $xmlPayload = new \SimpleXMLElement($xml);
            if ($error = libxml_get_last_error()) {
                throw new \RuntimeException($error->message);
            }
        } catch (\Exception $e) {
            throw new ParserException("Error parsing XML: {$e->getMessage()}", 0, $e);
        } finally {
            libxml_use_internal_errors($priorSetting);
        }

        return $xmlPayload;
    }
}
