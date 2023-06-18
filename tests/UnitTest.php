<?php

namespace IsaEken\HttpSnippet\Tests;

use GuzzleHttp\Psr7\Request;
use IsaEken\HttpSnippet\Exceptions\TargetNotFoundException;
use IsaEken\HttpSnippet\HttpSnippet;
use PHPUnit\Framework\TestCase;
use TypeError;

class UnitTest extends TestCase
{
    public function testIsThrowsWhenTargetNotFound()
    {
        $this->expectException(TargetNotFoundException::class);
        HttpSnippet::make(
            new Request(
                'GET',
                'http://example.com',
            ),
            'not-found',
        );
    }

    public function testIsThrowsWhenTargetIsNotSet()
    {
        $this->expectException(TypeError::class);
        $object = new HttpSnippet();
        $this->assertNull($object->getTarget());
    }

    public function testIsThrowsWhenRequestIsNotSet()
    {
        $this->expectException(TypeError::class);
        $object = new HttpSnippet();
        $this->assertNull($object->getRequest());
    }

    public function testIsCanSetTarget()
    {
        $targets = [
            'c.libcurl',
            'csharp.httpclient',
            'csharp.restsharp',
            'php.curl',
            'shell.curl',
            'shell.wget',
        ];

        foreach ($targets as $target) {
            $object = new HttpSnippet();

            $object->setTarget($target);
            $this->assertSame($object->getTarget()::info()['name'], $target);
        }

        foreach ($targets as $target) {
            $object = new HttpSnippet();

            $object->useTarget($target);
            $this->assertSame($object->getTarget()::info()['name'], $target);
        }
    }

    public function testIsCanSetRequest()
    {
        $object = new HttpSnippet();

        $object->setRequest(new Request(
            'GET',
            'http://example.com',
        ));

        $this->assertSame($object->getRequest()->getMethod(), 'GET');
        $this->assertSame($object->getRequest()->getUri()->__toString(), 'http://example.com');

        $object->setRequest(new Request(
            'GET',
            'http://isaeken.com.tr',
        ));

        $this->assertSame($object->getRequest()->getMethod(), 'GET');
        $this->assertSame($object->getRequest()->getUri()->__toString(), 'http://isaeken.com.tr');
    }
}
