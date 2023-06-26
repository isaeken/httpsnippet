<?php

namespace IsaEken\HttpSnippet\Traits\Request;

trait HasMethod
{
    public string $method;

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): self
    {
        return tap($this, fn (self $request) => $request->method = strtoupper($method));
    }
}
