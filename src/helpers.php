<?php

use IsaEken\HttpSnippet\Contracts\Language;

if (! function_exists('escapeForDoubleQuotes')) {
    /**
     * Dummy function to escape string for double quotes.
     *
     * @example "Hello \"World\"!" => "Hello \\"World\\"!"
     */
    function escapeForDoubleQuotes(string $string): string
    {
        return addcslashes($string, '"\\');
    }
}

if (! function_exists('getHttpSnippetLanguageName')) {
    function getHttpSnippetLanguageName(string|Language $language): string
    {
        if (is_string($language)) {
            if (class_exists($language) === false) {
                throw new InvalidArgumentException('Language class does not exists.');
            }

            $language = new $language();
        }

        if ($language instanceof Language) {
            throw new InvalidArgumentException('Language class must be instance of Language.');
        }

        $name = $language::info()['name'];
        unset($language);

        return $name;
    }
}
