<?php

namespace IsaEken\HttpSnippet\Contracts;

use IsaEken\HttpSnippet\CodeGenerator;
use JetBrains\PhpStorm\ArrayShape;

interface Language
{
    #[ArrayShape([
        'name' => 'string',
        'title' => 'string',
        'link' => 'string',
        'description' => 'string',
    ])]
    public static function info(): array;

    public function make(): CodeGenerator;

    public function toArray(): array;

    public function toString(): string;
}
