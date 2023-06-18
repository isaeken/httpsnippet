<?php

namespace IsaEken\HttpSnippet\Targets;

use IsaEken\HttpSnippet\Contracts\Target;
use IsaEken\HttpSnippet\HttpSnippet;

abstract class AbstractTarget implements Target
{
    private HttpSnippet $httpSnippet;

    public function __construct(HttpSnippet $httpSnippet)
    {
        $this->httpSnippet = $httpSnippet;
    }

    public function getHttpSnippet(): HttpSnippet
    {
        return $this->httpSnippet;
    }

    public function setHttpSnippet(HttpSnippet $httpSnippet): self
    {
        $this->httpSnippet = $httpSnippet;

        return $this;
    }

    public function toArray(): array
    {
        return $this->make()->toArray();
    }

    public function toString(): string
    {
        return $this->make()->toString();
    }
}
