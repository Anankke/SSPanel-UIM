<?php
namespace GuzzleHttp\Tests\Psr7;

use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\UploadedFile;
use GuzzleHttp\Psr7\Uri;

/**
 * @covers GuzzleHttp\Psr7\ServerRequest
 */
class ServerRequestTest extends \PHPUnit_Framework_TestCase
{
    public function dataNormalizeFiles()
    {
        return [
            'Single file' => [
                [
                    'file' => [
                        'name' => 'MyFile.txt',
                        'type' => 'text/plain',
                        'tmp_name' => '/tmp/php/php1h4j1o',
                        'error' => '0',
                        'size' => '123'
                    ]
                ],
                [
                    'file' => new UploadedFile(
                        '/tmp/php/php1h4j1o',
                        123,
                        UPLOAD_ERR_OK,
                        'MyFile.txt',
                        'text/plain'
                    )
                ]
            ],
            'Empty file' => [
                [
                    'image_file' => [
                        'name' => '',
                        'type' => '',
                        'tmp_name' => '',
                        'error' => '4',
                        'size' => '0'
                    ]
                ],
                [
                    'image_file' => new UploadedFile(
                        '',
                        0,
                        UPLOAD_ERR_NO_FILE,
                        '',
                        ''
                    )
                ]
            ],
            'Already Converted' => [
                [
                    'file' => new UploadedFile(
                        '/tmp/php/php1h4j1o',
                        123,
                        UPLOAD_ERR_OK,
                        'MyFile.txt',
                        'text/plain'
                    )
                ],
                [
                    'file' => new UploadedFile(
                        '/tmp/php/php1h4j1o',
                        123,
                        UPLOAD_ERR_OK,
                        'MyFile.txt',
                        'text/plain'
                    )
                ]
            ],
            'Already Converted array' => [
                [
                    'file' => [
                        new UploadedFile(
                            '/tmp/php/php1h4j1o',
                            123,
                            UPLOAD_ERR_OK,
                            'MyFile.txt',
                            'text/plain'
                        ),
                        new UploadedFile(
                            '',
                            0,
                            UPLOAD_ERR_NO_FILE,
                            '',
                            ''
                        )
                    ],
                ],
                [
                    'file' => [
                        new UploadedFile(
                            '/tmp/php/php1h4j1o',
                            123,
                            UPLOAD_ERR_OK,
                            'MyFile.txt',
                            'text/plain'
                        ),
                        new UploadedFile(
                            '',
                            0,
                            UPLOAD_ERR_NO_FILE,
                            '',
                            ''
                        )
                    ],
                ]
            ],
            'Multiple files' => [
                [
                    'text_file' => [
                        'name' => 'MyFile.txt',
                        'type' => 'text/plain',
                        'tmp_name' => '/tmp/php/php1h4j1o',
                        'error' => '0',
                        'size' => '123'
                    ],
                    'image_file' => [
                        'name' => '',
                        'type' => '',
                        'tmp_name' => '',
                        'error' => '4',
                        'size' => '0'
                    ]
                ],
                [
                    'text_file' => new UploadedFile(
                        '/tmp/php/php1h4j1o',
                        123,
                        UPLOAD_ERR_OK,
                        'MyFile.txt',
                        'text/plain'
                    ),
                    'image_file' => new UploadedFile(
                        '',
                        0,
                        UPLOAD_ERR_NO_FILE,
                        '',
                        ''
                    )
                ]
            ],
            'Nested files' => [
                [
                    'file' => [
                        'name' => [
                            0 => 'MyFile.txt',
                            1 => 'Image.png',
                        ],
                        'type' => [
                            0 => 'text/plain',
                            1 => 'image/png',
                        ],
                        'tmp_name' => [
                            0 => '/tmp/php/hp9hskjhf',
                            1 => '/tmp/php/php1h4j1o',
                        ],
                        'error' => [
                            0 => '0',
                            1 => '0',
                        ],
                        'size' => [
                            0 => '123',
                            1 => '7349',
                        ],
                    ],
                    'nested' => [
                        'name' => [
                            'other' => 'Flag.txt',
                            'test' => [
                                0 => 'Stuff.txt',
                                1 => '',
                            ],
                        ],
                        'type' => [
                            'other' => 'text/plain',
                            'test' => [
                                0 => 'text/plain',
                                1 => '',
                            ],
                        ],
                        'tmp_name' => [
                            'other' => '/tmp/php/hp9hskjhf',
                            'test' => [
                                0 => '/tmp/php/asifu2gp3',
                                1 => '',
                            ],
                        ],
                        'error' => [
                            'other' => '0',
                            'test' => [
                                0 => '0',
                                1 => '4',
                            ],
                        ],
                        'size' => [
                            'other' => '421',
                            'test' => [
                                0 => '32',
                                1 => '0',
                            ]
                        ]
                    ],
                ],
                [
                    'file' => [
                        0 => new UploadedFile(
                            '/tmp/php/hp9hskjhf',
                            123,
                            UPLOAD_ERR_OK,
                            'MyFile.txt',
                            'text/plain'
                        ),
                        1 => new UploadedFile(
                            '/tmp/php/php1h4j1o',
                            7349,
                            UPLOAD_ERR_OK,
                            'Image.png',
                            'image/png'
                        ),
                    ],
                    'nested' => [
                        'other' => new UploadedFile(
                            '/tmp/php/hp9hskjhf',
                            421,
                            UPLOAD_ERR_OK,
                            'Flag.txt',
                            'text/plain'
                        ),
                        'test' => [
                            0 => new UploadedFile(
                                '/tmp/php/asifu2gp3',
                                32,
                                UPLOAD_ERR_OK,
                                'Stuff.txt',
                                'text/plain'
                            ),
                            1 => new UploadedFile(
                                '',
                                0,
                                UPLOAD_ERR_NO_FILE,
                                '',
                                ''
                            ),
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider dataNormalizeFiles
     */
    public function testNormalizeFiles($files, $expected)
    {
        $result = ServerRequest::normalizeFiles($files);

        $this->assertEquals($expected, $result);
    }

    public function testNormalizeFilesRaisesException()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid value in files specification');

        ServerRequest::normalizeFiles(['test' => 'something']);
    }

    public function dataGetUriFromGlobals()
    {
        $server = [
            'PHP_SELF' => '/blog/article.php',
            'GATEWAY_INTERFACE' => 'CGI/1.1',
            'SERVER_ADDR' => 'Server IP: 217.112.82.20',
            'SERVER_NAME' => 'www.blakesimpson.co.uk',
            'SERVER_SOFTWARE' => 'Apache/2.2.15 (Win32) JRun/4.0 PHP/5.2.13',
            'SERVER_PROTOCOL' => 'HTTP/1.0',
            'REQUEST_METHOD' => 'POST',
            'REQUEST_TIME' => 'Request start time: 1280149029',
            'QUERY_STRING' => 'id=10&user=foo',
            'DOCUMENT_ROOT' => '/path/to/your/server/root/',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'HTTP_ACCEPT_ENCODING' => 'gzip,deflate',
            'HTTP_ACCEPT_LANGUAGE' => 'en-gb,en;q=0.5',
            'HTTP_CONNECTION' => 'keep-alive',
            'HTTP_HOST' => 'www.blakesimpson.co.uk',
            'HTTP_REFERER' => 'http://previous.url.com',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-GB; rv:1.9.2.6) Gecko/20100625 Firefox/3.6.6 ( .NET CLR 3.5.30729)',
            'HTTPS' => '1',
            'REMOTE_ADDR' => '193.60.168.69',
            'REMOTE_HOST' => 'Client server\'s host name',
            'REMOTE_PORT' => '5390',
            'SCRIPT_FILENAME' => '/path/to/this/script.php',
            'SERVER_ADMIN' => 'webmaster@blakesimpson.co.uk',
            'SERVER_PORT' => '80',
            'SERVER_SIGNATURE' => 'Version signature: 5.123',
            'SCRIPT_NAME' => '/blog/article.php',
            'REQUEST_URI' => '/blog/article.php?id=10&user=foo',
        ];

        return [
            'Normal request' => [
                'http://www.blakesimpson.co.uk/blog/article.php?id=10&user=foo',
                $server,
            ],
            'Secure request' => [
                'https://www.blakesimpson.co.uk/blog/article.php?id=10&user=foo',
                array_merge($server, ['HTTPS' => 'on', 'SERVER_PORT' => '443']),
            ],
            'HTTP_HOST missing' => [
                'http://www.blakesimpson.co.uk/blog/article.php?id=10&user=foo',
                array_merge($server, ['HTTP_HOST' => null]),
            ],
            'No query String' => [
                'http://www.blakesimpson.co.uk/blog/article.php',
                array_merge($server, ['REQUEST_URI' => '/blog/article.php', 'QUERY_STRING' => '']),
            ],
            'Different port' => [
                'http://www.blakesimpson.co.uk:8324/blog/article.php?id=10&user=foo',
                array_merge($server, ['SERVER_PORT' => '8324']),
            ],
            'Empty server variable' => [
                '',
                [],
            ],
        ];
    }

    /**
     * @dataProvider dataGetUriFromGlobals
     */
    public function testGetUriFromGlobals($expected, $serverParams)
    {
        $_SERVER = $serverParams;

        $this->assertEquals(new Uri($expected), ServerRequest::getUriFromGlobals());
    }

    public function testFromGlobals()
    {
        $_SERVER = [
            'PHP_SELF' => '/blog/article.php',
            'GATEWAY_INTERFACE' => 'CGI/1.1',
            'SERVER_ADDR' => 'Server IP: 217.112.82.20',
            'SERVER_NAME' => 'www.blakesimpson.co.uk',
            'SERVER_SOFTWARE' => 'Apache/2.2.15 (Win32) JRun/4.0 PHP/5.2.13',
            'SERVER_PROTOCOL' => 'HTTP/1.0',
            'REQUEST_METHOD' => 'POST',
            'REQUEST_TIME' => 'Request start time: 1280149029',
            'QUERY_STRING' => 'id=10&user=foo',
            'DOCUMENT_ROOT' => '/path/to/your/server/root/',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'HTTP_ACCEPT_ENCODING' => 'gzip,deflate',
            'HTTP_ACCEPT_LANGUAGE' => 'en-gb,en;q=0.5',
            'HTTP_CONNECTION' => 'keep-alive',
            'HTTP_HOST' => 'www.blakesimpson.co.uk',
            'HTTP_REFERER' => 'http://previous.url.com',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-GB; rv:1.9.2.6) Gecko/20100625 Firefox/3.6.6 ( .NET CLR 3.5.30729)',
            'HTTPS' => '1',
            'REMOTE_ADDR' => '193.60.168.69',
            'REMOTE_HOST' => 'Client server\'s host name',
            'REMOTE_PORT' => '5390',
            'SCRIPT_FILENAME' => '/path/to/this/script.php',
            'SERVER_ADMIN' => 'webmaster@blakesimpson.co.uk',
            'SERVER_PORT' => '80',
            'SERVER_SIGNATURE' => 'Version signature: 5.123',
            'SCRIPT_NAME' => '/blog/article.php',
            'REQUEST_URI' => '/blog/article.php?id=10&user=foo',
        ];

        $_COOKIE = [
            'logged-in' => 'yes!'
        ];

        $_POST = [
            'name' => 'Pesho',
            'email' => 'pesho@example.com',
        ];

        $_GET = [
            'id' => 10,
            'user' => 'foo',
        ];

        $_FILES = [
            'file' => [
                'name' => 'MyFile.txt',
                'type' => 'text/plain',
                'tmp_name' => '/tmp/php/php1h4j1o',
                'error' => UPLOAD_ERR_OK,
                'size' => 123,
            ]
        ];

        $server = ServerRequest::fromGlobals();

        $this->assertEquals('POST', $server->getMethod());
        $this->assertEquals(['Host' => ['www.blakesimpson.co.uk']], $server->getHeaders());
        $this->assertEquals('', (string) $server->getBody());
        $this->assertEquals('1.0', $server->getProtocolVersion());
        $this->assertEquals($_COOKIE, $server->getCookieParams());
        $this->assertEquals($_POST, $server->getParsedBody());
        $this->assertEquals($_GET, $server->getQueryParams());

        $this->assertEquals(
            new Uri('http://www.blakesimpson.co.uk/blog/article.php?id=10&user=foo'),
            $server->getUri()
        );

        $expectedFiles = [
            'file' => new UploadedFile(
                '/tmp/php/php1h4j1o',
                123,
                UPLOAD_ERR_OK,
                'MyFile.txt',
                'text/plain'
            ),
        ];

        $this->assertEquals($expectedFiles, $server->getUploadedFiles());
    }

    public function testUploadedFiles()
    {
        $request1 = new ServerRequest('GET', '/');

        $files = [
            'file' => new UploadedFile('test', 123, UPLOAD_ERR_OK)
        ];

        $request2 = $request1->withUploadedFiles($files);

        $this->assertNotSame($request2, $request1);
        $this->assertSame([], $request1->getUploadedFiles());
        $this->assertSame($files, $request2->getUploadedFiles());
    }

    public function testServerParams()
    {
        $params = ['name' => 'value'];

        $request = new ServerRequest('GET', '/', [], null, '1.1', $params);
        $this->assertSame($params, $request->getServerParams());
    }

    public function testCookieParams()
    {
        $request1 = new ServerRequest('GET', '/');

        $params = ['name' => 'value'];

        $request2 = $request1->withCookieParams($params);

        $this->assertNotSame($request2, $request1);
        $this->assertEmpty($request1->getCookieParams());
        $this->assertSame($params, $request2->getCookieParams());
    }

    public function testQueryParams()
    {
        $request1 = new ServerRequest('GET', '/');

        $params = ['name' => 'value'];

        $request2 = $request1->withQueryParams($params);

        $this->assertNotSame($request2, $request1);
        $this->assertEmpty($request1->getQueryParams());
        $this->assertSame($params, $request2->getQueryParams());
    }

    public function testParsedBody()
    {
        $request1 = new ServerRequest('GET', '/');

        $params = ['name' => 'value'];

        $request2 = $request1->withParsedBody($params);

        $this->assertNotSame($request2, $request1);
        $this->assertEmpty($request1->getParsedBody());
        $this->assertSame($params, $request2->getParsedBody());
    }

    public function testAttributes()
    {
        $request1 = new ServerRequest('GET', '/');

        $request2 = $request1->withAttribute('name', 'value');
        $request3 = $request2->withAttribute('other', 'otherValue');
        $request4 = $request3->withoutAttribute('other');
        $request5 = $request3->withoutAttribute('unknown');

        $this->assertNotSame($request2, $request1);
        $this->assertNotSame($request3, $request2);
        $this->assertNotSame($request4, $request3);
        $this->assertNotSame($request5, $request4);

        $this->assertEmpty($request1->getAttributes());
        $this->assertEmpty($request1->getAttribute('name'));
        $this->assertEquals(
            'something',
            $request1->getAttribute('name', 'something'),
            'Should return the default value'
        );

        $this->assertEquals('value', $request2->getAttribute('name'));
        $this->assertEquals(['name' => 'value'], $request2->getAttributes());
        $this->assertEquals(['name' => 'value', 'other' => 'otherValue'], $request3->getAttributes());
        $this->assertEquals(['name' => 'value'], $request4->getAttributes());
    }

    public function testNullAttribute()
    {
        $request = (new ServerRequest('GET', '/'))->withAttribute('name', null);

        $this->assertSame(['name' => null], $request->getAttributes());
        $this->assertNull($request->getAttribute('name', 'different-default'));

        $requestWithoutAttribute = $request->withoutAttribute('name');

        $this->assertSame([], $requestWithoutAttribute->getAttributes());
        $this->assertSame('different-default', $requestWithoutAttribute->getAttribute('name', 'different-default'));
    }
}
