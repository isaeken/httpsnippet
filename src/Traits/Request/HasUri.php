<?php

namespace IsaEken\HttpSnippet\Traits\Request;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Arr;
use Psr\Http\Message\UriInterface;

trait HasUri
{
    public UriInterface $uri;

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface|array|string $uri, bool $preserveHost = false): self
    {
        return tap($this, function (self $request) use ($uri) {
            if (is_array($uri)) {
                $url = Arr::first($uri);
                $query = Arr::except($uri, 0);
                $uri = new Uri($url);
                $uri = $uri->withQuery(http_build_query($query));
            }

            if (! ($uri instanceof UriInterface)) {
                $uri = new Uri($uri);
            }

            return $request->uri = $uri;
        });
    }
}
