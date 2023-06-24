<?php

namespace IsaEken\HttpSnippet\Abstracts;

use IsaEken\HttpSnippet\CodeGenerator;
use IsaEken\HttpSnippet\Contracts\Language;
use IsaEken\HttpSnippet\Traits\HasHttpSnippet;
use JetBrains\PhpStorm\ArrayShape;

abstract class AbstractLanguage implements Language
{
    use HasHttpSnippet;

    public static string|null $name = null;
    public static string|null $title = null;
    public static string|null $link = null;
    public static string|null $description = null;

    #[ArrayShape([
        'name' => 'string',
        'title' => 'string',
        'link' => 'string',
        'description' => 'string',
    ])]
    public static function info(): array
    {
        if (in_array(null, [
            static::$name,
            static::$title,
            static::$link,
            static::$description,
        ])) {
            $classname = explode('\\', static::class);
            $classname = end($classname);
            $classname = str($classname);

            return [
                'name' => static::$name ?? $classname->snake('.'),
                'title' => static::$title ?? $classname->title(),
                'link' => static::$link ?? '',
                'description' => static::$description ?? '',
            ];
        }

        return [
            'name' => static::$name,
            'title' => static::$title,
            'link' => static::$link,
            'description' => static::$description,
        ];
    }

    abstract public function make(): CodeGenerator;

    public function toArray(): array
    {
        return $this->make()->toArray();
    }

    public function toString(): string
    {
        return $this->make()->toString();
    }
}
