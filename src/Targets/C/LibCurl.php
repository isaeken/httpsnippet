<?php

namespace IsaEken\HttpSnippet\Targets\C;

use IsaEken\HttpSnippet\CodeGenerator;
use IsaEken\HttpSnippet\Contracts\Target;
use IsaEken\HttpSnippet\Targets\AbstractTarget;
use JetBrains\PhpStorm\ArrayShape;

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

    public function make(): CodeGenerator
    {
        $code = new CodeGenerator();
        $method = $this->getHttpSnippet()->getRequest()->getMethod();
        $uri = (string) $this->getHttpSnippet()->getRequest()->getUri();
        $headers = $this->getHttpSnippet()->getRequest()->getHeaders();
        $cookies = $this->getHttpSnippet()->getCookies();
        $body = $this->getHttpSnippet()->getRequest()->getBody()->getContents();

        $code->addLine('CURL *hnd = curl_easy_init();');
        $code->addLine('');
        $code->addLine(sprintf('curl_easy_setopt(hnd, CURLOPT_CUSTOMREQUEST, "%s");', $method));
        $code->addLine(sprintf('curl_easy_setopt(hnd, CURLOPT_URL, "%s");', $uri));

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

        if ($cookies->count() > 0) {
            $cookies = $cookies->toArray();
            $cookies = array_map(function ($key, $cookie) {
                $key = escapeForDoubleQuotes($cookie['Name']);
                $value = escapeForDoubleQuotes($cookie['Value']);

                return sprintf('%s=%s', $key, $value);
            }, array_keys($cookies), $cookies);
            $cookies = implode('; ', $cookies);
            $code->addLine(sprintf('curl_easy_setopt(hnd, CURLOPT_COOKIE, "%s");', $cookies));
        }

        if (strlen($body) > 0) {
            $code->addLine(sprintf('curl_easy_setopt(hnd, CURLOPT_POSTFIELDS, "%s");', escapeForDoubleQuotes($body)));
        }

        $code->addLine('');
        $code->addLine('CURLcode ret = curl_easy_perform(hnd);');
        $code->addLine('');
        $code->addLine('curl_easy_cleanup(hnd);');
        $code->addLine('curl_slist_free_all(headers);');

        return $code;
    }
}
