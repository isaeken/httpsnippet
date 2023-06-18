<?php

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
