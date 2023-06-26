<?php

namespace IsaEken\HttpSnippet\Traits\Request;

trait HasProtocolVersion
{
    public string $protocol = '1.1';

    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    public function withProtocolVersion(string $version): self
    {
        return tap($this, fn (self $request) => $request->protocol = $version);
    }
}
