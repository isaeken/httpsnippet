<?php

namespace IsaEken\HttpSnippet\Targets\CSharp;

use IsaEken\HttpSnippet\CodeGenerator;
use IsaEken\HttpSnippet\Contracts\Target;
use IsaEken\HttpSnippet\Enums\ContentType;
use IsaEken\HttpSnippet\Targets\AbstractTarget;
use JetBrains\PhpStorm\ArrayShape;

class RestSharp extends AbstractTarget implements Target
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
            'name' => 'csharp.restsharp',
            'title' => 'RestSharp',
            'link' => 'https://restsharp.dev/',
            'description' => 'Simple REST and HTTP API Client for .NET.',
        ];
    }

    public function make(): CodeGenerator
    {
        $code = new CodeGenerator();
        $uri = (string) $this->getHttpSnippet()->getRequest()->getUri();
        $method = $this->getHttpSnippet()->getRequest()->getMethod();
        $headers = $this->getHttpSnippet()->getRequest()->getHeaders();
        $cookies = $this->getHttpSnippet()->getCookies();
        $body = $this->getHttpSnippet()->getRequest()->getBody();
        $contentType = $this->getHttpSnippet()->getContentType();

        $code->addLine(sprintf('var client = new RestClient("%s");', $uri));
        $code->addLine(sprintf('var request = new RestRequest(Method.%s);', $method));
        $code->addLine('');

        foreach ($headers as $key => $values) {
            if (strtolower($key) === 'cookie') {
                continue;
            }

            $value = implode(', ', $values);
            $code->addLine(sprintf('request.AddHeader("%s", "%s");', $key, escapeForDoubleQuotes($value)));
            $code->addLine('');
        }

        foreach ($cookies as $cookie) {
            $name = $cookie->getName();
            $value = escapeForDoubleQuotes($cookie->getValue());
            $code->addLine(sprintf('request.AddCookie("%s", "%s");', $name, $value));
            $code->addLine('');
        }

        if ($body->getSize() > 0) {
            if ($contentType === ContentType::FORM) {
                $body = json_decode($body->getContents(), true);
                $code->addLine('var requestParams = new StringBuilder();');
                $code->addLine('');

                foreach ($body as $key => $value) {
                    $code->addLine(sprintf('requestParams.Append("%s=%s&");', $key, escapeForDoubleQuotes($value)));
                }

                $code->addLine('request.AddParameter("application/x-www-form-urlencoded", requestParams, ParameterType.RequestBody);');
                $code->addLine('');
            } elseif ($contentType === ContentType::MULTIPART) {
                $body = json_decode($body->getContents(), true);
                $code->addLine('var requestParams = new StringBuilder();');
                $code->addLine('');

                foreach ($body as $key => $value) {
                    $code->addLine(sprintf('requestParams.Append("%s=%s&");', $key, escapeForDoubleQuotes($value)));
                }

                $code->addLine('request.AddParameter("multipart/form-data", requestParams, ParameterType.RequestBody);');
                $code->addLine('');
            } elseif ($contentType === ContentType::JSON) {
                $body = json_decode($body->getContents(), true);
                $code->addLine('request.AddJsonBody(');
                foreach ($body as $key => $value) {
                    $code->addLine(sprintf('"%s": "%s",', escapeForDoubleQuotes($key), escapeForDoubleQuotes($value)), 1);
                }
                $code->addLine(');');
                $code->addLine('');
            } elseif ($contentType === ContentType::RAW) {
                $code->addLine(sprintf('request.AddParameter("%s", %s, ParameterType.RequestBody);', $headers['content-type'][0], escapeForDoubleQuotes($body->getContents())));
                $code->addLine('');
            }
        }

        $code->addLine('IRestResponse response = client.Execute(request);');
        $code->addLine('Console.WriteLine(response.Content);');

        return $code;
    }
}
