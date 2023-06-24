<?php

namespace IsaEken\HttpSnippet\Exceptions;

use Exception;

class LanguageNotFoundException extends Exception
{
    public function __construct(string $language)
    {
        parent::__construct("Language not found: $language");
    }
}
