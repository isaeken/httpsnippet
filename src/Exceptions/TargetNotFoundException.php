<?php

namespace IsaEken\HttpSnippet\Exceptions;

use Exception;

class TargetNotFoundException extends Exception
{
    public function __construct(string $target)
    {
        parent::__construct("Target not found: $target");
    }
}
