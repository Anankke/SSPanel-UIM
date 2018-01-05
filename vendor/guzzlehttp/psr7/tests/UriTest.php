<?php
namespace GuzzleHttp\Tests\Psr7;

use GuzzleHttp\Psr7\Uri;

/**
 * @covers GuzzleHttp\Psr7\Uri
 */
class UriTest extends \PHPUnit_Framework_TestCase
{
    const RFC3986_BASE = 'http://a/b/c/d;p?q';

    public function testParsesProvidedUri()
    {
        $uri = new Uri('https://user:pass@example.com:8080/path/123?q=abc#test');

        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('user:pass@example.com:8080', $uri->getAuthority());
        $this->assertSame('user:pass', $uri->getUserInfo());
        $this->assertSame('example.com', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('/path/123', $uri->getPath());
        $this->assertSame('q=abc', $uri->getQuery());
        $this->assertSame('test', $uri->getFragment());
        $this->assertSame('https://user:pass@example.com:8080/path/123?q=abc#test', (string) $uri);
    }

    public function testCanTransformAndRetrievePartsIndividually()
    {
        $uri = (new Uri())
            ->withScheme('https')
            ->withUserInfo('user', 'pass')
            ->withHost('example.com')
            ->withPort(8080)
            ->withPath('/path/123')
            ->withQuery('q=abc')
            ->withFragment('test');

        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('user:pass@example.com:8080', $uri->getAuthority());
        $this->assertSame('user:pass', $uri->getUserInfo());
        $this->assertSame('example.com', $uri->getHost());
        $this->assertSame(8080, $uri->getPort());
        $this->assertSame('/path/123', $uri->getPath());
        $this->assertSame('q=abc', $uri->getQuery());
        $this->assertSame('test', $uri->getFragment());
        $this->assertSame('https://user:pass@example.com:8080/path/123?q=abc#test', (string) $uri);
    }

    /**
     * @dataProvider getValidUris
     */
    public function testValidUrisStayValid($input)
    {
        $uri = new Uri($input);

        $this->assertSame($input, (string) $uri);
    }

    /**
     * @dataProvider getValidUris
     */
    public function testFromParts($input)
    {
        $uri = Uri::fromParts(parse_url($input));

        $this->assertSame($input, (string) $uri);
    }

    public function getValidUris()
    {
        return [
            ['urn:path-rootless'],
            ['urn:path:with:colon'],
            ['urn:/path-absolute'],
            ['urn:/'],
            // only scheme with empty path
            ['urn:'],
            // only path
            ['/'],
            ['relative/'],
            ['0'],
            // same document reference
            [''],
            // network path without scheme
            ['//example.org'],
            ['//example.org/'],
            ['//example.org?q#h'],
            // only query
            ['?q'],
            ['?q=abc&foo=bar'],
            // only fragment
            ['#fragment'],
            // dot segments are not removed automatically
            ['./foo/../bar'],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unable to parse URI
     * @dataProvider getInvalidUris
     */
    public function testInvalidUrisThrowException($invalidUri)
    {
        new Uri($invalidUri);
    }

    public function getInvalidUris()
    {
        return [
            // parse_url() requires the host component which makes sense for http(s)
            // but not when the scheme is not known or different. So '//' or '///' is
            // currently invalid as well but should not according to RFC 3986.
            ['http://'],
            ['urn://host:with:colon'], // host cannot contain ":"
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid port: 100000. Must be between 1 and 65535
     */
    public function testPortMustBeValid()
    {
        (new Uri())->withPort(100000);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid port: 0. Must be between 1 and 65535
     */
    public function testWithPortCannotBeZero()
    {
        (new Uri())->withPort(0);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unable to parse URI
     */
    public function testParseUriPortCannotBeZero()
    {
        new Uri('//example.com:0');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSchemeMustHaveCorrectType()
    {
        (new Uri())->withScheme([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testHostMustHaveCorrectType()
    {
        (new Uri())->withHost([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPathMustHaveCorrectType()
    {
        (new Uri())->withPath([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testQueryMustHaveCorrectType()
    {
        (new Uri())->withQuery([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFragmentMustHaveCorrectType()
    {
        (new Uri())->withFragment([]);
    }

    public function testCanParseFalseyUriParts()
    {
        $uri = new Uri('0://0:0@0/0?0#0');

        $this->assertSame('0', $uri->getScheme());
        $this->assertSame('0:0@0', $uri->getAuthority());
        $this->assertSame('0:0', $uri->getUserInfo());
        $this->assertSame('0', $uri->getHost());
        $this->assertSame('/0', $uri->getPath());
        $this->assertSame('0', $uri->getQuery());
        $this->assertSame('0', $uri->getFragment());
        $this->assertSame('0://0:0@0/0?0#0', (string) $uri);
    }

    public function testCanConstructFalseyUriParts()
    {
        $uri = (new Uri())
            ->withScheme('0')
            ->withUserInfo('0', '0')
            ->withHost('0')
            ->withPath('/0')
            ->withQuery('0')
            ->withFragment('0');

        $this->assertSame('0', $uri->getScheme());
        $this->assertSame('0:0@0', $uri->getAuthority());
        $this->assertSame('0:0', $uri->getUserInfo());
        $this->assertSame('0', $uri->getHost());
        $this->assertSame('/0', $uri->getPath());
        $this->assertSame('0', $uri->getQuery());
        $this->assertSame('0', $uri->getFragment());
        $this->assertSame('0://0:0@0/0?0#0', (string) $uri);
    }

    /**
     * @dataProvider getResolveTestCases
     */
    public function testResolvesUris($base, $rel, $expected)
    {
        $uri = new Uri($base);
        $actual = Uri::resolve($uri, $rel);
        $this->assertSame($expected, (string) $actual);
    }

    public function getResolveTestCases()
    {
        return [
            [self::RFC3986_BASE, 'g:h',           'g:h'],
            [self::RFC3986_BASE, 'g',             'http://a/b/c/g'],
            [self::RFC3986_BASE, './g',           'http://a/b/c/g'],
            [self::RFC3986_BASE, 'g/',            'http://a/b/c/g/'],
            [self::RFC3986_BASE, '/g',            'http://a/g'],
            [self::RFC3986_BASE, '//g',           'http://g'],
            [self::RFC3986_BASE, '?y',            'http://a/b/c/d;p?y'],
            [self::RFC3986_BASE, 'g?y',           'http://a/b/c/g?y'],
            [self::RFC3986_BASE, '#s',            'http://a/b/c/d;p?q#s'],
            [self::RFC3986_BASE, 'g#s',           'http://a/b/c/g#s'],
            [self::RFC3986_BASE, 'g?y#s',         'http://a/b/c/g?y#s'],
            [self::RFC3986_BASE, ';x',            'http://a/b/c/;x'],
            [self::RFC3986_BASE, 'g;x',           'http://a/b/c/g;x'],
            [self::RFC3986_BASE, 'g;x?y#s',       'http://a/b/c/g;x?y#s'],
            [self::RFC3986_BASE, '',              self::RFC3986_BASE],
            [self::RFC3986_BASE, '.',             'http://a/b/c/'],
            [self::RFC3986_BASE, './',            'http://a/b/c/'],
            [self::RFC3986_BASE, '..',            'http://a/b/'],
            [self::RFC3986_BASE, '../',           'http://a/b/'],
            [self::RFC3986_BASE, '../g',          'http://a/b/g'],
            [self::RFC3986_BASE, '../..',         'http://a/'],
            [self::RFC3986_BASE, '../../',        'http://a/'],
            [self::RFC3986_BASE, '../../g',       'http://a/g'],
            [self::RFC3986_BASE, '../../../g',    'http://a/g'],
            [self::RFC3986_BASE, '../../../../g', 'http://a/g'],
            [self::RFC3986_BASE, '/./g',          'http://a/g'],
            [self::RFC3986_BASE, '/../g',         'http://a/g'],
            [self::RFC3986_BASE, 'g.',            'http://a/b/c/g.'],
            [self::RFC3986_BASE, '.g',            'http://a/b/c/.g'],
            [self::RFC3986_BASE, 'g..',           'http://a/b/c/g..'],
            [self::RFC3986_BASE, '..g',           'http://a/b/c/..g'],
            [self::RFC3986_BASE, './../g',        'http://a/b/g'],
            [self::RFC3986_BASE, 'foo////g',      'http://a/b/c/foo////g'],
            [self::RFC3986_BASE, './g/.',         'http://a/b/c/g/'],
            [self::RFC3986_BASE, 'g/./h',         'http://a/b/c/g/h'],
            [self::RFC3986_BASE, 'g/../h',        'http://a/b/c/h'],
            [self::RFC3986_BASE, 'g;x=1/./y',     'http://a/b/c/g;x=1/y'],
            [self::RFC3986_BASE, 'g;x=1/../y',    'http://a/b/c/y'],
            // dot-segments in the query or fragment
            [self::RFC3986_BASE, 'g?y/./x',       'http://a/b/c/g?y/./x'],
            [self::RFC3986_BASE, 'g?y/../x',      'http://a/b/c/g?y/../x'],
            [self::RFC3986_BASE, 'g#s/./x',       'http://a/b/c/g#s/./x'],
            [self::RFC3986_BASE, 'g#s/../x',      'http://a/b/c/g#s/../x'],
            [self::RFC3986_BASE, 'g#s/../x',      'http://a/b/c/g#s/../x'],
            [self::RFC3986_BASE, '?y#s',          'http://a/b/c/d;p?y#s'],
            ['http://a/b/c/d;p?q#s', '?y',        'http://a/b/c/d;p?y'],
            ['http://u@a/b/c/d;p?q', '.',         'http://u@a/b/c/'],
            ['http://u:p@a/b/c/d;p?q', '.',       'http://u:p@a/b/c/'],
            ['http://a/b/c/d/', 'e',              'http://a/b/c/d/e'],
            ['urn:no-slash', 'e',                 'urn:e'],
            // falsey relative parts
            [self::RFC3986_BASE, '//0',           'http://0'],
            [self::RFC3986_BASE, '0',             'http://a/b/c/0'],
            [self::RFC3986_BASE, '?0',            'http://a/b/c/d;p?0'],
            [self::RFC3986_BASE, '#0',            'http://a/b/c/d;p?q#0'],
        ];
    }

    public function testAddAndRemoveQueryValues()
    {
        $uri = new Uri();
        $uri = Uri::withQueryValue($uri, 'a', 'b');
        $uri = Uri::withQueryValue($uri, 'c', 'd');
        $uri = Uri::withQueryValue($uri, 'e', null);
        $this->assertSame('a=b&c=d&e', $uri->getQuery());

        $uri = Uri::withoutQueryValue($uri, 'c');
        $this->assertSame('a=b&e', $uri->getQuery());
        $uri = Uri::withoutQueryValue($uri, 'e');
        $this->assertSame('a=b', $uri->getQuery());
        $uri = Uri::withoutQueryValue($uri, 'a');
        $this->assertSame('', $uri->getQuery());
    }

    public function testWithQueryValueReplacesSameKeys()
    {
        $uri = new Uri();
        $uri = Uri::withQueryValue($uri, 'a', 'b');
        $uri = Uri::withQueryValue($uri, 'c', 'd');
        $uri = Uri::withQueryValue($uri, 'a', 'e');
        $this->assertSame('c=d&a=e', $uri->getQuery());
    }

    public function testWithoutQueryValueRemovesAllSameKeys()
    {
        $uri = (new Uri())->withQuery('a=b&c=d&a=e');
        $uri = Uri::withoutQueryValue($uri, 'a');
        $this->assertSame('c=d', $uri->getQuery());
    }

    public function testRemoveNonExistingQueryValue()
    {
        $uri = new Uri();
        $uri = Uri::withQueryValue($uri, 'a', 'b');
        $uri = Uri::withoutQueryValue($uri, 'c');
        $this->assertSame('a=b', $uri->getQuery());
    }

    public function testWithQueryValueHandlesEncoding()
    {
        $uri = new Uri();
        $uri = Uri::withQueryValue($uri, 'E=mc^2', 'ein&stein');
        $this->assertSame('E%3Dmc%5E2=ein%26stein', $uri->getQuery(), 'Decoded key/value get encoded');

        $uri = new Uri();
        $uri = Uri::withQueryValue($uri, 'E%3Dmc%5e2', 'ein%26stein');
        $this->assertSame('E%3Dmc%5e2=ein%26stein', $uri->getQuery(), 'Encoded key/value do not get double-encoded');
    }

    public function testWithoutQueryValueHandlesEncoding()
    {
        // It also tests that the case of the percent-encoding does not matter,
        // i.e. both lowercase "%3d" and uppercase "%5E" can be removed.
        $uri = (new Uri())->withQuery('E%3dmc%5E2=einstein&foo=bar');
        $uri = Uri::withoutQueryValue($uri, 'E=mc^2');
        $this->assertSame('foo=bar', $uri->getQuery(), 'Handles key in decoded form');

        $uri = (new Uri())->withQuery('E%3dmc%5E2=einstein&foo=bar');
        $uri = Uri::withoutQueryValue($uri, 'E%3Dmc%5e2');
        $this->assertSame('foo=bar', $uri->getQuery(), 'Handles key in encoded form');
    }

    public function testSchemeIsNormalizedToLowercase()
    {
        $uri = new Uri('HTTP://example.com');

        $this->assertSame('http', $uri->getScheme());
        $this->assertSame('http://example.com', (string) $uri);

        $uri = (new Uri('//example.com'))->withScheme('HTTP');

        $this->assertSame('http', $uri->getScheme());
        $this->assertSame('http://example.com', (string) $uri);
    }

    public function testHostIsNormalizedToLowercase()
    {
        $uri = new Uri('//eXaMpLe.CoM');

        $this->assertSame('example.com', $uri->getHost());
        $this->assertSame('//example.com', (string) $uri);

        $uri = (new Uri())->withHost('eXaMpLe.CoM');

        $this->assertSame('example.com', $uri->getHost());
        $this->assertSame('//example.com', (string) $uri);
    }

    public function testPortIsNullIfStandardPortForScheme()
    {
        // HTTPS standard port
        $uri = new Uri('https://example.com:443');
        $this->assertNull($uri->getPort());
        $this->assertSame('example.com', $uri->getAuthority());

        $uri = (new Uri('https://example.com'))->withPort(443);
        $this->assertNull($uri->getPort());
        $this->assertSame('example.com', $uri->getAuthority());

        // HTTP standard port
        $uri = new Uri('http://example.com:80');
        $this->assertNull($uri->getPort());
        $this->assertSame('example.com', $uri->getAuthority());

        $uri = (new Uri('http://example.com'))->withPort(80);
        $this->assertNull($uri->getPort());
        $this->assertSame('example.com', $uri->getAuthority());
    }

    public function testPortIsReturnedIfSchemeUnknown()
    {
        $uri = (new Uri('//example.com'))->withPort(80);

        $this->assertSame(80, $uri->getPort());
        $this->assertSame('example.com:80', $uri->getAuthority());
    }

    public function testStandardPortIsNullIfSchemeChanges()
    {
        $uri = new Uri('http://example.com:443');
        $this->assertSame('http', $uri->getScheme());
        $this->assertSame(443, $uri->getPort());

        $uri = $uri->withScheme('https');
        $this->assertNull($uri->getPort());
    }

    public function testPortPassedAsStringIsCastedToInt()
    {
        $uri = (new Uri('//example.com'))->withPort('8080');

        $this->assertSame(8080, $uri->getPort(), 'Port is returned as integer');
        $this->assertSame('example.com:8080', $uri->getAuthority());
    }

    public function testPortCanBeRemoved()
    {
        $uri = (new Uri('http://example.com:8080'))->withPort(null);

        $this->assertNull($uri->getPort());
        $this->assertSame('http://example.com', (string) $uri);
    }

    public function testAuthorityWithUserInfoButWithoutHost()
    {
        $uri = (new Uri())->withUserInfo('user', 'pass');

        $this->assertSame('user:pass', $uri->getUserInfo());
        $this->assertSame('', $uri->getAuthority());
    }

    public function uriComponentsEncodingProvider()
    {
        $unreserved = 'a-zA-Z0-9.-_~!$&\'()*+,;=:@';

        return [
            // Percent encode spaces
            ['/pa th?q=va lue#frag ment', '/pa%20th', 'q=va%20lue', 'frag%20ment', '/pa%20th?q=va%20lue#frag%20ment'],
            // Percent encode multibyte
            ['/€?€#€', '/%E2%82%AC', '%E2%82%AC', '%E2%82%AC', '/%E2%82%AC?%E2%82%AC#%E2%82%AC'],
            // Don't encode something that's already encoded
            ['/pa%20th?q=va%20lue#frag%20ment', '/pa%20th', 'q=va%20lue', 'frag%20ment', '/pa%20th?q=va%20lue#frag%20ment'],
            // Percent encode invalid percent encodings
            ['/pa%2-th?q=va%2-lue#frag%2-ment', '/pa%252-th', 'q=va%252-lue', 'frag%252-ment', '/pa%252-th?q=va%252-lue#frag%252-ment'],
            // Don't encode path segments
            ['/pa/th//two?q=va/lue#frag/ment', '/pa/th//two', 'q=va/lue', 'frag/ment', '/pa/th//two?q=va/lue#frag/ment'],
            // Don't encode unreserved chars or sub-delimiters
            ["/$unreserved?$unreserved#$unreserved", "/$unreserved", $unreserved, $unreserved, "/$unreserved?$unreserved#$unreserved"],
            // Encoded unreserved chars are not decoded
            ['/p%61th?q=v%61lue#fr%61gment', '/p%61th', 'q=v%61lue', 'fr%61gment', '/p%61th?q=v%61lue#fr%61gment'],
        ];
    }

    /**
     * @dataProvider uriComponentsEncodingProvider
     */
    public function testUriComponentsGetEncodedProperly($input, $path, $query, $fragment, $output)
    {
        $uri = new Uri($input);
        $this->assertSame($path, $uri->getPath());
        $this->assertSame($query, $uri->getQuery());
        $this->assertSame($fragment, $uri->getFragment());
        $this->assertSame($output, (string) $uri);
    }

    public function testWithPathEncodesProperly()
    {
        $uri = (new Uri())->withPath('/baz?#€/b%61r');
        // Query and fragment delimiters and multibyte chars are encoded.
        $this->assertSame('/baz%3F%23%E2%82%AC/b%61r', $uri->getPath());
        $this->assertSame('/baz%3F%23%E2%82%AC/b%61r', (string) $uri);
    }

    public function testWithQueryEncodesProperly()
    {
        $uri = (new Uri())->withQuery('?=#&€=/&b%61r');
        // A query starting with a "?" is valid and must not be magically removed. Otherwise it would be impossible to
        // construct such an URI. Also the "?" and "/" does not need to be encoded in the query.
        $this->assertSame('?=%23&%E2%82%AC=/&b%61r', $uri->getQuery());
        $this->assertSame('??=%23&%E2%82%AC=/&b%61r', (string) $uri);
    }

    public function testWithFragmentEncodesProperly()
    {
        $uri = (new Uri())->withFragment('#€?/b%61r');
        // A fragment starting with a "#" is valid and must not be magically removed. Otherwise it would be impossible to
        // construct such an URI. Also the "?" and "/" does not need to be encoded in the fragment.
        $this->assertSame('%23%E2%82%AC?/b%61r', $uri->getFragment());
        $this->assertSame('#%23%E2%82%AC?/b%61r', (string) $uri);
    }

    public function testAllowsForRelativeUri()
    {
        $uri = (new Uri)->withPath('foo');
        $this->assertSame('foo', $uri->getPath());
        $this->assertSame('foo', (string) $uri);
    }

    public function testAddsSlashForRelativeUriStringWithHost()
    {
        // If the path is rootless and an authority is present, the path MUST
        // be prefixed by "/".
        $uri = (new Uri)->withPath('foo')->withHost('example.com');
        $this->assertSame('foo', $uri->getPath());
        // concatenating a relative path with a host doesn't work: "//example.comfoo" would be wrong
        $this->assertSame('//example.com/foo', (string) $uri);
    }

    public function testRemoveExtraSlashesWihoutHost()
    {
        // If the path is starting with more than one "/" and no authority is
        // present, the starting slashes MUST be reduced to one.
        $uri = (new Uri)->withPath('//foo');
        $this->assertSame('//foo', $uri->getPath());
        // URI "//foo" would be interpreted as network reference and thus change the original path to the host
        $this->assertSame('/foo', (string) $uri);
    }

    public function testDefaultReturnValuesOfGetters()
    {
        $uri = new Uri();

        $this->assertSame('', $uri->getScheme());
        $this->assertSame('', $uri->getAuthority());
        $this->assertSame('', $uri->getUserInfo());
        $this->assertSame('', $uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertSame('', $uri->getPath());
        $this->assertSame('', $uri->getQuery());
        $this->assertSame('', $uri->getFragment());
    }

    public function testImmutability()
    {
        $uri = new Uri();

        $this->assertNotSame($uri, $uri->withScheme('https'));
        $this->assertNotSame($uri, $uri->withUserInfo('user', 'pass'));
        $this->assertNotSame($uri, $uri->withHost('example.com'));
        $this->assertNotSame($uri, $uri->withPort(8080));
        $this->assertNotSame($uri, $uri->withPath('/path/123'));
        $this->assertNotSame($uri, $uri->withQuery('q=abc'));
        $this->assertNotSame($uri, $uri->withFragment('test'));
    }

    public function testExtendingClassesInstantiates()
    {
        // The non-standard port triggers a cascade of private methods which
        //  should not use late static binding to access private static members.
        // If they do, this will fatal.
        $this->assertInstanceOf(
            '\GuzzleHttp\Tests\Psr7\ExtendingClassTest',
            new ExtendingClassTest('http://h:9/')
        );
    }
}

class ExtendingClassTest extends \GuzzleHttp\Psr7\Uri
{
}
