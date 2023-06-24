<?php

namespace IsaEken\HttpSnippet\Traits;

use IsaEken\HttpSnippet\Contracts\Language;

trait HasLanguage
{
    private Language $language;

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): self
    {
        return tap($this, function () use ($language) {
            $this->language = $language;
        });
    }

    public function useLanguage(Language $language): self
    {
        return $this->setLanguage($language);
    }
}
