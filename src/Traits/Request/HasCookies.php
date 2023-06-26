<?php

namespace IsaEken\HttpSnippet\Traits\Request;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Psr\Http\Message\UriInterface;

trait HasCookies
{
    public CookieJar $cookies;

    public function getCookies(): CookieJar
    {
        return $this->cookies;
    }

    public function withCookies(CookieJar $cookies): self
    {
        return tap($this, fn (self $request) => $request->cookies = $cookies);
    }

    public function addCookie(string $key, string $value): self
    {
        return tap($this, function (self $request) use ($key, $value) {
            $request->cookies->setCookie(new SetCookie([
                'Name' => $key,
                'Value' => $value,
                'Domain' => $request->getUri()->getHost(),
                'Path' => $request->getUri()->getPath(),
            ]));
        });
    }

    public static function makeCookiesFromHeaders(mixed $object, UriInterface|string $uri): CookieJar
    {
        $cookies = [];
        $cookieHeaderLine = $object->getHeaderLine('Cookie');

        if ($cookieHeaderLine) {
            $cookiePairs = explode('; ', $cookieHeaderLine);

            foreach ($cookiePairs as $cookiePair) {
                [$name, $value] = explode('=', $cookiePair, 2);

                $cookies[] = [
                    'Name' => $name,
                    'Value' => $value,
                    'Domain' => $uri instanceof UriInterface ? $uri->getHost() : $uri,
                    'Path' => $uri instanceof UriInterface ? $uri->getPath() : '/',
                ];
            }
        }

        $cookieJar = $object->cookies ?? new CookieJar();
        foreach ($cookies as $cookie) {
            $cookieJar->setCookie(new SetCookie($cookie));
        }

        return $cookieJar;
    }
}
