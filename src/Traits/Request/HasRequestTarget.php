<?php

namespace IsaEken\HttpSnippet\Traits\Request;

trait HasRequestTarget
{
    public string $requestTarget = '/';

    public function getRequestTarget(): string
    {
        return $this->requestTarget;
    }

    public function withRequestTarget($requestTarget): self
    {
        return tap($this, fn (self $request) => $request->requestTarget = $requestTarget);
    }
}
