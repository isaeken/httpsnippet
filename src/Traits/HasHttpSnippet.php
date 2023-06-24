<?php

namespace IsaEken\HttpSnippet\Traits;

use IsaEken\HttpSnippet\HttpSnippet;

trait HasHttpSnippet
{
    private HttpSnippet $httpSnippet;

    public function getHttpSnippet(): HttpSnippet
    {
        return $this->httpSnippet;
    }

    public function setHttpSnippet(HttpSnippet $httpSnippet): self
    {
        return tap($this, function () use ($httpSnippet) {
            $this->httpSnippet = $httpSnippet;
        });
    }

    public function useHttpSnippet(HttpSnippet $httpSnippet): self
    {
        return $this->setHttpSnippet($httpSnippet);
    }
}
