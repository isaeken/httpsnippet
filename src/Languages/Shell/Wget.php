<?php

namespace IsaEken\HttpSnippet\Languages\Shell;

use IsaEken\HttpSnippet\Abstracts\AbstractLanguage;
use IsaEken\HttpSnippet\CodeGenerator;
use IsaEken\HttpSnippet\Contracts\Language;
use JetBrains\PhpStorm\ArrayShape;

class Wget extends AbstractLanguage implements Language
{
    public static string|null $name = 'shell.wget';
    public static string|null $title = 'Shell Wget';
    public static string|null $link = 'https://www.gnu.org/software/wget/';
    public static string|null $description = 'Wget is a free software package for retrieving files using HTTP, HTTPS';

    public function make(): CodeGenerator
    {
        $code = new CodeGenerator(
            intents: 2,
            divider: " \\",
        );

        $request = $this->getHttpSnippet()->getRequest();
        $uri = (string) $request->getUri();
        $method = $request->getMethod();
        $headers = $request->getHeaders();
        $body = $request->getBody();

        $code->addLine('wget');
        $code->addLine('--method="'.$method.'"', 1);

        foreach ($headers as $key => $values) {
            $value = implode(', ', $values);
            $code->addLine(sprintf('--header="%s: %s"', $key, escapeForDoubleQuotes($value)), 1);
        }

        if ($body->getSize() > 0) {
            $code->addLine('--body-data="'.escapeForDoubleQuotes($body->getContents()).'"', 1);
        }

        $code->addLine('"'.escapeForDoubleQuotes($uri).'"', 1);

        return $code;
    }
}
