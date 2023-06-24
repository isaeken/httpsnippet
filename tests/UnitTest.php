<?php

namespace IsaEken\HttpSnippet\Tests;

use Error;
use GuzzleHttp\Psr7\Request;
use IsaEken\HttpSnippet\Exceptions\LanguageNotFoundException;
use IsaEken\HttpSnippet\HttpSnippet;
use PHPUnit\Framework\TestCase;

class UnitTest extends TestCase
{
    public function testIsThrowsWhenLanguageNotFound()
    {
        $this->expectException(LanguageNotFoundException::class);
        HttpSnippet::make(
            new Request(
                'GET',
                'http://example.com',
            ),
            'not-found',
        );
    }

    public function testIsThrowsWhenLanguageIsNotSet()
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('must not be accessed before initialization');
        $object = new HttpSnippet();
        $this->assertNull($object->getLanguage());
    }

    public function testIsThrowsWhenRequestIsNotSet()
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage('must not be accessed before initialization');
        $object = new HttpSnippet();
        $this->assertNull($object->getRequest());
    }

    public function testIsCanSetLanguage()
    {
        $languages = [
            'c.libcurl',
            'csharp.httpclient',
            'csharp.restsharp',
            'php.curl',
            'shell.curl',
            'shell.wget',
        ];

        foreach ($languages as $language) {
            $object = new HttpSnippet();

            $object->setLanguage(new ($object->getLanguages()[$language]));
            $this->assertSame($object->getLanguage()::info()['name'], $language);
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
