<?php

namespace IsaEken\HttpSnippet;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Cookie\SetCookie;
use Illuminate\Support\Arr;
use IsaEken\HttpSnippet\Contracts\Target;
use IsaEken\HttpSnippet\Enums\ContentType;
use IsaEken\HttpSnippet\Exceptions\TargetNotFoundException;
use Psr\Http\Message\RequestInterface;

class HttpSnippet
{
    public static array $targets = [
        'c.libcurl' => Targets\C\LibCurl::class,
        'csharp.httpclient' => Targets\CSharp\HttpClient::class,
        'csharp.restsharp' => Targets\CSharp\RestSharp::class,
        'php.curl' => Targets\Php\Curl::class,
        'shell.curl' => Targets\Shell\Curl::class,
        'shell.wget' => Targets\Shell\Wget::class,
    ];

    public RequestInterface|null $request = null;

    public Target|null $target = null;

    public function setTarget(string $target): void
    {
        if (! array_key_exists($target, self::$targets)) {
            throw new TargetNotFoundException($target);
        }

        $this->target = new (self::$targets[$target])($this);
    }

    public function useTarget(string $target): HttpSnippet
    {
        return tap($this, fn () => $this->setTarget($target));
    }

    public function getTarget(): Target
    {
        return $this->target;
    }

    public function setRequest(RequestInterface $request): void
    {
        $this->request = $request;
    }

    public function useRequest(RequestInterface $request): HttpSnippet
    {
        return tap($this, fn () => $this->setRequest($request));
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function isJson(): bool
    {
        return $this->getContentType() === ContentType::JSON;
    }

    public function getContentType(): ContentType
    {
        $contentType = $this->getRequest()->getHeaderLine('Content-Type');

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

    public function getCookies(): CookieJarInterface
    {
        $request = $this->getRequest();

        $cookies = [];
        $cookieHeaderLine = $request->getHeaderLine('Cookie');

        if ($cookieHeaderLine) {
            $cookiePairs = explode('; ', $cookieHeaderLine);

            foreach ($cookiePairs as $cookiePair) {
                [$name, $value] = explode('=', $cookiePair, 2);

                $cookies[] = [
                    'Name' => $name,
                    'Value' => $value,
                    'Domain' => $request->getUri()->getHost(),
                    'Path' => $request->getUri()->getPath(),
                ];
            }
        }

        $cookieJar = new CookieJar();
        foreach ($cookies as $cookie) {
            $cookieJar->setCookie(new SetCookie($cookie));
        }

        return $cookieJar;
    }

    public static function make(RequestInterface $request, string $target): static
    {
        return (new static())
            ->useRequest($request)
            ->useTarget($target);
    }

    /**
     * @return array<array{name: string, title: string, link: string, description: string}>
     */
    public static function getTargets(): array
    {
        return Arr::map(array_values(self::$targets), fn ($target) => $target::info());
    }
}
