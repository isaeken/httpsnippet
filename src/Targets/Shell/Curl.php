<?php

namespace IsaEken\HttpSnippet\Targets\Shell;

use IsaEken\HttpSnippet\CodeGenerator;
use IsaEken\HttpSnippet\Contracts\Target;
use IsaEken\HttpSnippet\Enums\ContentType;
use IsaEken\HttpSnippet\Targets\AbstractTarget;
use JetBrains\PhpStorm\ArrayShape;

class Curl extends AbstractTarget implements Target
{
    #[ArrayShape([
        'name' => 'string',
        'title' => 'string',
        'link' => 'string',
        'description' => 'string',
    ])]
    public static function info(): array
    {
        return [
            'name' => 'shell.curl',
            'title' => 'cURL',
            'link' => 'https://curl.haxx.se/',
            'description' => 'cURL is a command line tool and library for transferring data with URL syntax',
        ];
    }

    public function make(): CodeGenerator
    {
        $code = new CodeGenerator(
            intents: 2,
            divider: " \\"
        );

        $uri = (string) $this->getHttpSnippet()->getRequest()->getUri();
        $method = $this->getHttpSnippet()->getRequest()->getMethod();
        $headers = $this->getHttpSnippet()->getRequest()->getHeaders();
        $cookies = $this->getHttpSnippet()->getCookies();
        $body = $this->getHttpSnippet()->getRequest()->getBody();
        $contentType = $this->getHttpSnippet()->getContentType();

        $code->addLine('curl');

        foreach ($headers as $key => $values) {
            if (strtolower($key) === 'cookie') {
                continue;
            }

            $value = implode(', ', $values);
            $code->addLine(sprintf('--header "%s: %s"', $key, escapeForDoubleQuotes($value)), 1);
        }

        foreach ($cookies as $cookie) {
            $name = $cookie->getName();
            $value = escapeForDoubleQuotes($cookie->getValue());
            $code->addLine(sprintf('--cookie "%s=%s"', $name, $value), 1);
        }

        if ($body->getSize() > 0) {
            if ($contentType === ContentType::FORM) {
                $body = json_decode($body->getContents(), true);
                $code->addLine('--data-urlencode "'.http_build_query($body).'"', 1);
            } else {
                $code->addLine('--data \''.escapeForDoubleQuotes($body->getContents()).'\'', 1);
            }
        }

        $code->addLine('--request "'.$method.'"', 1);
        $code->addLine('--url "'.$uri.'"', 1);

        return $code;
    }
}
