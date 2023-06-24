<?php

namespace IsaEken\HttpSnippet\Languages\Shell;

use IsaEken\HttpSnippet\Abstracts\AbstractLanguage;
use IsaEken\HttpSnippet\CodeGenerator;
use IsaEken\HttpSnippet\Enums\ContentType;

class Curl extends AbstractLanguage
{
    public static string|null $name = 'shell.curl';
    public static string|null $title = 'Shell cURL';
    public static string|null $link = 'https://curl.haxx.se/';
    public static string|null $description = 'cURL is a command line tool and library for transferring data with URL syntax';

    public function make(): CodeGenerator
    {
        $code = new CodeGenerator(
            intents: 2,
            divider: " \\"
        );

        $request = $this->getHttpSnippet()->getRequest();
        $uri = (string) $request->getUri();
        $method = $request->getMethod();
        $headers = $request->getHeaders();
        $cookies = $request->getCookies();
        $body = $request->getBody();
        $contentType = $request->getContentType();

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
