<?php

namespace IsaEken\HttpSnippet;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Cookie\SetCookie;
use IsaEken\HttpSnippet\Enums\ContentType;
use Psr\Http\Message\RequestInterface;

class Request extends \GuzzleHttp\Psr7\Request implements RequestInterface
{
    public function getContentType(): ContentType
    {
        $contentType = $this->getHeaderLine('Content-Type');

        if (str_contains($contentType, 'application/json')) {
            return ContentType::JSON;
        }

        if (str_contains($contentType, 'application/x-www-form-urlencoded')) {
            return ContentType::FORM;
        }

        if (str_contains($contentType, 'multipart/form-data')) {
            return ContentType::MULTIPART;
        }

        return ContentType::RAW;
    }

    public function isJson(): bool
    {
        return $this->getContentType() === ContentType::JSON;
    }

    public function getCookies(): CookieJarInterface
    {
        $cookies = [];
        $cookieHeaderLine = $this->getHeaderLine('Cookie');

        if ($cookieHeaderLine) {
            $cookiePairs = explode('; ', $cookieHeaderLine);

            foreach ($cookiePairs as $cookiePair) {
                [$name, $value] = explode('=', $cookiePair, 2);

                $cookies[] = [
                    'Name' => $name,
                    'Value' => $value,
                    'Domain' => $this->getUri()->getHost(),
                    'Path' => $this->getUri()->getPath(),
                ];
            }
        }

        $cookieJar = new CookieJar();
        foreach ($cookies as $cookie) {
            $cookieJar->setCookie(new SetCookie($cookie));
        }

        return $cookieJar;
    }

    public static function fromPsrRequest(RequestInterface $request): self
    {
        return new self(
            $request->getMethod(),
            $request->getUri(),
            $request->getHeaders(),
            $request->getBody(),
            $request->getProtocolVersion()
        );
    }
}
