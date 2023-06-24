<?php

namespace IsaEken\HttpSnippet\Traits;

use IsaEken\HttpSnippet\Request;
use Psr\Http\Message\RequestInterface;

trait HasRequest
{
    private Request $request;

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setRequest(Request|RequestInterface $request): self
    {
        return tap($this, function () use ($request) {
            if ($request instanceof RequestInterface) {
                $request = Request::fromPsrRequest($request);
            }

            $this->request = $request;
        });
    }

    public function useRequest(Request|RequestInterface $request): self
    {
        return $this->setRequest($request);
    }
}
