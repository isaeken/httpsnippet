<?php

namespace IsaEken\HttpSnippet\Contracts;

use IsaEken\HttpSnippet\CodeGenerator;
use IsaEken\HttpSnippet\HttpSnippet;
use JetBrains\PhpStorm\ArrayShape;

interface Target
{
    #[ArrayShape([
        'name' => 'string',
        'title' => 'string',
        'link' => 'string',
        'description' => 'string',
    ])]
    public static function info(): array;

    public function __construct(HttpSnippet $httpSnippet);

    public function make(): CodeGenerator;

    public function toArray(): array;

    public function toString(): string;
}
