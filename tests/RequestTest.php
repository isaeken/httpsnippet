<?php

namespace IsaEken\HttpSnippet\Tests;

use GuzzleHttp\Psr7\Request;
use IsaEken\HttpSnippet\HttpSnippet;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
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
            $output = trim(str_replace("\r\n", "\n", $output));
            $result = trim(str_replace("\r\n", "\n", $httpSnippet->toString()));
            $this->assertSame($output, $result);
        }
    }
}
