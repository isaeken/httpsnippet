<?php

namespace IsaEken\HttpSnippet\Languages\CSharp;

use IsaEken\HttpSnippet\Abstracts\AbstractLanguage;
use IsaEken\HttpSnippet\CodeGenerator;
use IsaEken\HttpSnippet\Contracts\Language;
use IsaEken\HttpSnippet\Enums\ContentType;

class RestSharp extends AbstractLanguage implements Language
{
    public static string|null $name = 'csharp.restsharp';
    public static string|null $title = 'C# RestSharp';
    public static string|null $link = 'https://restsharp.dev/';
    public static string|null $description = 'Simple REST and HTTP API Client for .NET.';

    public function make(): CodeGenerator
    {
        $code = new CodeGenerator();
        $request = $this->getHttpSnippet()->getRequest();
        $uri = (string) $request->getUri();
        $method = $request->getMethod();
        $headers = $request->getHeaders();
        $cookies = $request->getCookies();
        $body = $request->getBody();
        $contentType = $request->getContentType();

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
