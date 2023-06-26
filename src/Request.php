<?php

namespace IsaEken\HttpSnippet;

use Illuminate\Support\Arr;
use IsaEken\HttpSnippet\Traits\Request\HasBody;
use IsaEken\HttpSnippet\Traits\Request\HasCookies;
use IsaEken\HttpSnippet\Traits\Request\HasHeaders;
use IsaEken\HttpSnippet\Traits\Request\HasMethod;
use IsaEken\HttpSnippet\Traits\Request\HasProtocolVersion;
use IsaEken\HttpSnippet\Traits\Request\HasRequestTarget;
use IsaEken\HttpSnippet\Traits\Request\HasUri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{
    use HasBody;
    use HasHeaders;
    use HasMethod;
    use HasProtocolVersion;
    use HasRequestTarget;
    use HasUri;
    use HasCookies;

    public function __construct(
        string $method,
        UriInterface|array|string $uri,
        array $headers = [],
        mixed $body = null,
        string $version = '1.1',
        array $cookies = [],
    ) {
        $this
            ->withMethod($method)
            ->withUri($uri)
            ->withHeaders($headers)
            ->withBody($body)
            ->withProtocolVersion($version)
            ->withRequestTarget($this->getUri()->getPath())
            ->withCookies(static::makeCookiesFromHeaders($this, $this->getUri()->getHost()));

        foreach ($cookies as $name => $value) {
            $this->addCookie($name, $value);
        }

        if ($this->getCookies()->count() > 0) {
            $cookies = $this->getCookies()->toArray();
            $cookies = array_map(fn ($cookie) => $cookie['Name'].'='.$cookie['Value'], $cookies);
            $this->withAddedHeader('Cookie', $cookies);
        }
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

    public static function fromGuzzleRequest(\GuzzleHttp\Psr7\Request $request): self
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
