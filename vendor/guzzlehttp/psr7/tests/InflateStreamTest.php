<?php
namespace GuzzleHttp\Tests\Psr7;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\InflateStream;

class InflateStreamtest extends \PHPUnit_Framework_TestCase
{
    public function testInflatesStreams()
    {
        $content = gzencode('test');
        $a = Psr7\stream_for($content);
        $b = new InflateStream($a);
        $this->assertEquals('test', (string) $b);
    }

    public function testInflatesStreamsWithFilename()
    {
        $content = $this->getGzipStringWithFilename('test');
        $a = Psr7\stream_for($content);
        $b = new InflateStream($a);
        $this->assertEquals('test', (string) $b);
    }

    private function getGzipStringWithFilename($original_string)
    {
        $gzipped = bin2hex(gzencode($original_string));

        $header = substr($gzipped, 0, 20);
        // set FNAME flag
        $header[6]=0;
        $header[7]=8;
        // make a dummy filename
        $filename = "64756d6d7900";
        $rest = substr($gzipped, 20);

        return hex2bin($header . $filename . $rest);
    }
}
