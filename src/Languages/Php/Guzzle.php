<?php

namespace IsaEken\HttpSnippet\Languages\Php;

use IsaEken\HttpSnippet\Abstracts\AbstractLanguage;
use IsaEken\HttpSnippet\CodeGenerator;
use IsaEken\HttpSnippet\Contracts\Language;
use IsaEken\HttpSnippet\Enums\ContentType;

class Guzzle extends AbstractLanguage implements Language
{
    public static string|null $name = 'php.guzzle';
    public static string|null $title = 'PHP Guzzle';
    public static string|null $link = 'https://docs.guzzlephp.org/en/stable/';
    public static string|null $description = 'PHP with guzzlehttp/guzzle.';

    private function makeBody($intent = 0): CodeGenerator
    {
        $code = new CodeGenerator(intent: $intent);
        $request = $this->getHttpSnippet()->getRequest();
        $contentType = $request->getContentType();

        if ($contentType === ContentType::MULTIPART) {
            $code->addLine("'multipart' => [");
            $data = $request->getMultipartData();

            foreach ($data as $object) {
                $code->addLine('[', 1);
                $code->addLines(array_filter([
                    $object['name'] ? "'name' => '".escapeForDoubleQuotes($object['name'])."'," : null,
                    $object['contents'] ? "'contents' => '".escapeForDoubleQuotes($object['contents'])."'," : null,
                    $object['file'] ? "'contents' => fopen('".escapeForDoubleQuotes($object['file'])."', 'r')," : null,
                    $object['filename'] ? "'filename' => '".escapeForDoubleQuotes($object['filename'])."'," : null,
                ]), 2);
                $code->addLine('],', 1);
            }

            $code->addLine('],');
        } elseif ($contentType === ContentType::FORM) {
            $code->addLine("'form_params' => [");
            $data = $request->getFormData();

            foreach ($data as $key => $value) {
                $code->addLine("'".escapeForDoubleQuotes($key)."' => '".escapeForDoubleQuotes($value)."',", 1);
            }

            $code->addLine('],');
        } else {
            $code->addLine("'body' => '".str_replace('\\', '\\\\', $request->getBody()->getContents())."',");
        }

        return $code;
    }

    private function makeContent($intent = 0): CodeGenerator
    {
        $code = new CodeGenerator(intent: $intent);
        $request = $this->getHttpSnippet()->getRequest();
        $port = $request->getUri()->getPort();
        $url = (string) $request->getUri();
        $method = $request->getMethod();
        $headers = $request->getHeaders();
        $cookies = $request->getCookies();

        $code->addLine("'$method',");
        $code->addLine("'$url',");

        if ($request->getBody()->getSize() > 0 || ! empty($headers) || $cookies->count() > 0) {
            $code->addLine('[');

            if ($request->getBody()->getSize() > 0) {
                $code->addLines([$this->makeBody(1)]);
            }

            if (! empty($headers)) {
                $code->addLine("'headers' => [", 1);

                foreach ($headers as $key => $value) {
                    $value = implode('; ', array_unique($value));
                    $key = str($key)->replace('_', '-')->title()->__toString();
                    $key = escapeForDoubleQuotes($key);
                    $value = escapeForDoubleQuotes($value);

                    $code->addLine("\"$key\" => \"$value\",", 2);
                }

                $code->addLine('],', 1);
            }

            $code->addLine('],');
        }

        return $code;
    }

    public function make(): CodeGenerator
    {
        $code = new CodeGenerator();

        $code->addLines([
            '<?php',
            '',
            '$client = new \\GuzzleHttp\\Client();',
            '',
            '$response = $client->request(',
            $this->makeContent(1),
            ');',
            '',
            'echo $response->getBody();',
        ]);

        return $code;
    }
}
