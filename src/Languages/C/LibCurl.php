<?php

namespace IsaEken\HttpSnippet\Languages\C;

use GuzzleHttp\Cookie\CookieJarInterface;
use IsaEken\HttpSnippet\Abstracts\AbstractLanguage;
use IsaEken\HttpSnippet\CodeGenerator;
use IsaEken\HttpSnippet\Contracts\Language;
use Psr\Http\Message\StreamInterface;

class LibCurl extends AbstractLanguage implements Language
{
    public static string|null $name = 'c.libcurl';
    public static string|null $title = 'C LibCurl';
    public static string|null $link = 'https://curl.se/libcurl/c/';
    public static string|null $description = 'Simple REST and HTTP API Client for C.';

    public function makeHeaders(array $headers, CodeGenerator $code): CodeGenerator
    {
        if (! empty($headers)) {
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
        $request = $this->getHttpSnippet()->getRequest();
        $method = $request->getMethod();
        $uri = (string) $request->getUri();
        $headers = $request->getHeaders();
        $cookies = $request->getCookies();
        $body = $request->getBody();
        $intent = 0;

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

        return $code;
    }
}
