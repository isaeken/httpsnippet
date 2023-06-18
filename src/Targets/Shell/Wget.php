<?php

namespace IsaEken\HttpSnippet\Targets\Shell;

use IsaEken\HttpSnippet\CodeGenerator;
use IsaEken\HttpSnippet\Contracts\Target;
use IsaEken\HttpSnippet\Targets\AbstractTarget;
use JetBrains\PhpStorm\ArrayShape;

class Wget extends AbstractTarget implements Target
{
    #[ArrayShape([
        'name' => 'string',
        'title' => 'string',
        'link' => 'string',
        'description' => 'string',
    ])]
    public static function info(): array
    {
        return [
            'name' => 'shell.wget',
            'title' => 'Wget',
            'link' => 'https://www.gnu.org/software/wget/',
            'description' => 'a free software package for retrieving files using HTTP, HTTPS',
        ];
    }

    public function make(): CodeGenerator
    {
        $code = new CodeGenerator(
            intents: 2,
            divider: " \\",
        );

        $uri = (string) $this->getHttpSnippet()->getRequest()->getUri();
        $method = $this->getHttpSnippet()->getRequest()->getMethod();
        $headers = $this->getHttpSnippet()->getRequest()->getHeaders();
        $body = $this->getHttpSnippet()->getRequest()->getBody();

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
