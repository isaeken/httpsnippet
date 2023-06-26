<?php

namespace IsaEken\HttpSnippet\Tests;

use IsaEken\HttpSnippet\HttpSnippet;
use IsaEken\HttpSnippet\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    private function fixLineEnds(string $string): string
    {
        return trim(str_replace("\r\n", "\n", $string));
    }

    private function runMockTests(Request $request, array $mocks): void
    {
        foreach ($mocks as $language => $output) {
            $httpSnippet = HttpSnippet::make($request, $language);
            $output = $this->fixLineEnds($output);
            $result = $this->fixLineEnds($httpSnippet->toString());
            $this->assertSame($output, $result);
        }
    }

    public function testBasicRequest()
    {
        $tests = [
            'c.libcurl' => file_get_contents(__DIR__.'/snippets/basic/c.libcurl.txt'),
            'csharp.httpclient' => file_get_contents(__DIR__.'/snippets/basic/csharp.httpclient.txt'),
            'csharp.restsharp' => file_get_contents(__DIR__.'/snippets/basic/csharp.restsharp.txt'),
            'php.curl' => file_get_contents(__DIR__.'/snippets/basic/php.curl.txt'),
            'shell.curl' => file_get_contents(__DIR__.'/snippets/basic/shell.curl.txt'),
            'shell.wget' => file_get_contents(__DIR__.'/snippets/basic/shell.wget.txt'),
        ];

        $request = new Request(
            'GET',
            'http://example.com',
        );

        foreach ($tests as $language => $output) {
            $httpSnippet = HttpSnippet::make($request, $language);
            $output = $this->fixLineEnds($output);
            $result = $this->fixLineEnds($httpSnippet->toString());
            $this->assertSame($output, $result);
        }
    }

    public function testTextPlain()
    {
        $this->runMockTests(
            new Request(
                'POST',
                'http://mockbin.com/har',
                [
                    'content-type' => 'text/plain',
                ],
                'Hello World',
            ),
            [
                'php.guzzle' => file_get_contents(__DIR__.'/snippets/php/guzzle/text-plain.php'),
            ],
        );
    }

    public function testShort()
    {
        $this->runMockTests(
            new Request(
                'GET',
                'http://mockbin.com/har',
            ),
            [
                'php.guzzle' => file_get_contents(__DIR__.'/snippets/php/guzzle/short.php'),
            ],
        );
    }

    public function testQuery()
    {
        $this->runMockTests(
            new Request(
                'GET',
                [
                    'http://mockbin.com/har',
                    'bar' => 'bar',
                    'baz' => 'baz',
                    'key' => 'value',
                ],
            ),
            [
                'php.guzzle' => file_get_contents(__DIR__.'/snippets/php/guzzle/query.php'),
            ],
        );
    }

    public function testMultipartFormDataNoParams()
    {
        $this->runMockTests(
            new Request(
                'POST',
                'http://mockbin.com/har',
                [
                    'content-type' => 'multipart/form-data',
                ],
            ),
            [
                'php.guzzle' => file_get_contents(__DIR__.'/snippets/php/guzzle/multipart-form-data-no-params.php'),
            ],
        );
    }

    public function testMultipartFormData()
    {
        $this->runMockTests(
            new Request(
                'POST',
                'http://mockbin.com/har',
                [
                    'Content-Type' => 'multipart/form-data',
                ],
                [
                    [
                        'name' => 'foo',
                        'contents' => 'bar',
                    ],
                ],
            ),
            [
                'php.guzzle' => file_get_contents(__DIR__.'/snippets/php/guzzle/multipart-form-data.php'),
            ],
        );
    }

    public function testMultipartFile()
    {
        $this->runMockTests(
            new Request(
                'POST',
                'http://mockbin.com/har',
                [
                    'Content-Type' => 'multipart/form-data',
                ],
                [
                    [
                        'name' => 'foo',
                        'file' => 'test/fixtures/files/hello.txt',
                        'filename' => 'test/fixtures/files/hello.txt',
                    ],
                ],
            ),
            [
                'php.guzzle' => file_get_contents(__DIR__.'/snippets/php/guzzle/multipart-file.php'),
            ],
        );
    }

    public function testMultipartData()
    {
        $this->runMockTests(
            new Request(
                'POST',
                'http://mockbin.com/har',
                [
                    'Content-Type' => 'multipart/form-data',
                ],
                [
                    [
                        'name' => 'foo',
                        'contents' => 'Hello World',
                        'filename' => 'hello.txt',
                    ],
                    [
                        'name' => 'bar',
                        'contents' => 'Bonjour le monde',
                    ],
                ],
            ),
            [
                'php.guzzle' => file_get_contents(__DIR__.'/snippets/php/guzzle/multipart-data.php'),
            ],
        );
    }

    public function testJsonObjectNullValue()
    {
        $this->runMockTests(
            new Request(
                'POST',
                'http://mockbin.com/har',
                [
                    'Content-Type' => 'application/json',
                ],
                [
                    'foo' => null,
                ],
            ),
            [
                'php.guzzle' => file_get_contents(__DIR__.'/snippets/php/guzzle/jsonObj-null-value.php'),
            ],
        );
    }

    public function testJsonObjectMultiline() {
        $this->runMockTests(
            new Request(
                'POST',
                'http://mockbin.com/har',
                [
                    'Content-Type' => 'application/json',
                ],
                json_encode([
                    'foo' => 'bar',
                ], JSON_PRETTY_PRINT),
            ),
            [
                'php.guzzle' => file_get_contents(__DIR__.'/snippets/php/guzzle/jsonObj-multiline.php'),
            ],
        );
    }

    public function testHttps()
    {
        $this->runMockTests(
            new Request(
                'GET',
                'https://mockbin.com/har',
            ),
            [
                'php.guzzle' => file_get_contents(__DIR__.'/snippets/php/guzzle/https.php'),
            ],
        );
    }

    public function testHeaders()
    {
        $this->runMockTests(
            new Request(
                'GET',
                'http://mockbin.com/har',
                [
                    'accept' => 'application/json',
                    'quoted-value' => '"quoted" \'string\'',
                    'x-foo' => 'Bar',
                ],
            ),
            [
                'php.guzzle' => file_get_contents(__DIR__.'/snippets/php/guzzle/headers.php'),
            ],
        );
    }

    public function testFull()
    {
        $this->runMockTests(
            new Request(
                'POST',
                [
                    'http://mockbin.com/har',
                    'foo' => 'bar',
                    'bar' => 'baz',
                    'baz' => 'abc',
                    'key' => 'value',
                ],
                [
                    'accept' => 'application/json',
                    'content-type' => 'application/x-www-form-urlencoded',
                ],
                [
                    'foo' => 'bar',
                ],
                cookies: [
                    'foo' => 'bar',
                    'bar' => 'baz',
                ],
            ),
            [
                'php.guzzle' => file_get_contents(__DIR__.'/snippets/php/guzzle/full.php'),
            ],
        );
    }

    public function testCustomMethod()
    {
        $this->runMockTests(
            new Request(
                'PROPFIND',
                'http://mockbin.com/har',
            ),
            [
                'php.guzzle' => file_get_contents(__DIR__.'/snippets/php/guzzle/custom-method.php'),
            ],
        );
    }

    public function testCookies()
    {
        $this->runMockTests(
            new Request(
                'POST',
                'http://mockbin.com/har',
                cookies: [
                    'foo' => 'bar',
                    'bar' => 'baz',
                ],
            ),
            [
                'php.guzzle' => file_get_contents(__DIR__.'/snippets/php/guzzle/cookies.php'),
            ],
        );
    }

    public function testApplicationJson()
    {
        $this->runMockTests(
            new Request(
                'POST',
                'http://mockbin.com/har',
                [
                    'Content-Type' => 'application/json',
                ],
                [
                    'number' => 1,
                    'string' => 'f"oo',
                    'arr' => [1,2,3],
                    'nested' => [
                        'a' => 'b',
                    ],
                    'arr_mix' => [
                        1,
                        'a',
                        [
                            'arr_mix_nested' => (object) [],
                        ],
                    ],
                    'boolean' => false,
                ],
            ),
            [
                'php.guzzle' => file_get_contents(__DIR__.'/snippets/php/guzzle/application-json.php'),
            ],
        );
    }

    public function testApplicationFormEncoded()
    {
        $this->runMockTests(
            new Request(
                'POST',
                'http://mockbin.com/har',
                [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                [
                    'foo' => 'bar',
                    'hello' => 'world',
                ],
            ),
            [
                'php.guzzle' => file_get_contents(__DIR__.'/snippets/php/guzzle/application-form-encoded.php'),
            ],
        );
    }
}
