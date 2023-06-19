<?php

namespace IsaEken\HttpSnippet\Targets\C;

use GuzzleHttp\Cookie\CookieJarInterface;
use IsaEken\HttpSnippet\CodeGenerator;
use IsaEken\HttpSnippet\Contracts\Target;
use IsaEken\HttpSnippet\Targets\AbstractTarget;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Http\Message\StreamInterface;

class LibCurl extends AbstractTarget implements Target
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
            'name' => 'c.libcurl',
            'title' => 'LibCurl',
            'link' => 'https://curl.se/libcurl/c/',
            'description' => 'Simple REST and HTTP API Client for C.',
        ];
    }

    public function makeHeaders(array $headers, CodeGenerator $code): CodeGenerator
    {
        if (count($headers) > 0) {
            $code->addLine('');
            $code->addLine('struct curl_slist *headers = NULL;');
            $code->addLine('');

            foreach ($headers as $key => $values) {
                $key = escapeForDoubleQuotes($key);
                $value = escapeForDoubleQuotes(implode('; ', $values));
                $code->addLine(sprintf('headers = curl_slist_append(headers, "%s: %s");', $key, $value));
            }

            $code->addLine('');
            $code->addLine('curl_easy_setopt(hnd, CURLOPT_HTTPHEADER, headers);');
        }

        return $code;
    }

    public function makeCookies(CookieJarInterface $cookieJar, CodeGenerator $code): CodeGenerator
    {
        if ($cookieJar->count() > 0) {
            $cookies = $cookieJar->toArray();
            $cookies = array_map(function ($key, $cookie) {
                $key = escapeForDoubleQuotes($cookie['Name']);
                $value = escapeForDoubleQuotes($cookie['Value']);

                return sprintf('%s=%s', $key, $value);
            }, array_keys($cookies), $cookies);
            $cookies = implode('; ', $cookies);
            $code->addLine(sprintf('curl_easy_setopt(hnd, CURLOPT_COOKIE, "%s");', $cookies));
        }

        return $code;
    }

    public function makeBody(StreamInterface $body, string $contentType, CodeGenerator $code): CodeGenerator
    {
        if ($body->getSize() > 0) {
            $code->addLine(sprintf('curl_easy_setopt(hnd, CURLOPT_POSTFIELDS, "%s");', escapeForDoubleQuotes($body->getContents())));
        }

        return $code;
    }

    public function make(): CodeGenerator
    {
        $code = new CodeGenerator();
        $method = $this->getHttpSnippet()->getRequest()->getMethod();
        $uri = (string) $this->getHttpSnippet()->getRequest()->getUri();
        $headers = $this->getHttpSnippet()->getRequest()->getHeaders();
        $cookies = $this->getHttpSnippet()->getCookies();
        $body = $this->getHttpSnippet()->getRequest()->getBody();
        $intent = 0;

        if ($this->getHttpSnippet()->generateFullCode) {
            $code->addLine('#include <curl/curl.h>');
            $code->addLine('');
            $code->addLine('int main(void) {');
            $intent = 1;
        }

        $content = new CodeGenerator(intent: $intent);

        $content->addLine('CURL *hnd = curl_easy_init();');
        $content->addEmptyLine();
        $content->addLine(sprintf('curl_easy_setopt(hnd, CURLOPT_CUSTOMREQUEST, "%s");', $method));
        $content->addLine(sprintf('curl_easy_setopt(hnd, CURLOPT_URL, "%s");', $uri));
        $this->makeHeaders($headers, $content);
        $this->makeCookies($cookies, $content);
        $this->makeBody($body, '', $content);
        $content->addEmptyLine();
        $content->addLine('CURLcode ret = curl_easy_perform(hnd);');
        $content->addEmptyLine();
        $content->addLine('curl_easy_cleanup(hnd);');
        $content->addLine('curl_slist_free_all(headers);');

        $code->addLines($content->toArray());

        if ($this->getHttpSnippet()->generateFullCode) {
            $code->addLine('return (int) ret;', 1);
            $code->addLine('}');
        }

        return $code;
    }
}
