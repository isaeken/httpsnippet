<?php

namespace IsaEken\HttpSnippet\Targets\CSharp;

use IsaEken\HttpSnippet\CodeGenerator;
use IsaEken\HttpSnippet\Contracts\Target;
use IsaEken\HttpSnippet\Enums\ContentType;
use IsaEken\HttpSnippet\Targets\AbstractTarget;
use JetBrains\PhpStorm\ArrayShape;

class HttpClient extends AbstractTarget implements Target
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
            'name' => 'csharp.httpclient',
            'title' => 'HttpClient',
            'link' => 'https://docs.microsoft.com/en-us/dotnet/api/system.net.http.httpclient',
            'description' => '.NET Standard HTTP client.',
        ];
    }

    private function getDecompressionMethods(array $headers): array
    {
        $acceptEncodings = $headers['accept-encoding'] ?? null;

        if (is_string($acceptEncodings)) {
            $acceptEncodings = explode(',', $acceptEncodings);
        }

        if (empty($acceptEncodings)) {
            return [];
        }

        $supportedMethods = [
            'gzip' => 'DecompressionMethods.GZip',
            'deflate' => 'DecompressionMethods.Deflate',
        ];

        $methods = [];

        foreach ($acceptEncodings as $acceptEncoding) {
            $encodings = explode(',', $acceptEncoding);

            foreach ($encodings as $encoding) {
                $match = preg_match('/\s*([^;\s]+)/', $encoding, $matches);

                if ($match) {
                    $method = $supportedMethods[$matches[1]];
                    if ($method) {
                        $methods[] = $method;
                    }
                }
            }
        }

        return $methods;
    }

    public function make(): CodeGenerator
    {
        $code = new CodeGenerator();
        $uri = (string) $this->getHttpSnippet()->getRequest()->getUri();
        $method = $this->getHttpSnippet()->getRequest()->getMethod();
        $headers = $this->getHttpSnippet()->getRequest()->getHeaders();
        $cookies = $this->getHttpSnippet()->getCookies();
        $body = $this->getHttpSnippet()->getRequest()->getBody()->getContents();
        $contentType = $this->getHttpSnippet()->getContentType();

        $code->addLine('using System.Net.Http.Headers;');
        $code->addLine('using System.Net.Http;');

        $clientHeader = '';
        $decompressionMethods = $this->getDecompressionMethods($headers);

        if (! empty($decompressionMethods) || ($cookies->count() > 0)) {
            $clientHeader = 'clientHeader';
            $code->addLine('var handler = new HttpClientHandler()');
            $code->addLine('{');

            if ($cookies->count() > 0) {
                $code->addLine('UseCookies = false,', 1);
            }

            if (! empty($decompressionMethods)) {
                $code->addLine('AutomaticDecompression = '.implode(' | ', $decompressionMethods).',', 1);
            }

            $code->addLine('};');
        }

        $code->addLine(sprintf('var client = new HttpClient(%s);', $clientHeader));
        $code->addLine('');
        $code->addLine('var request = new HttpRequestMessage');
        $code->addLine('{');
        $code->addLine(sprintf('Method = HttpMethod.%s,', ucfirst(strtolower($method))), 1);
        $code->addLine(sprintf('RequestUri = new Uri("%s"),', escapeForDoubleQuotes($uri)), 1);

        $headers = array_filter($headers, function ($key) {
            return ! in_array(strtolower($key), [
                'content-type',
                'content-length',
                'accept-encoding',
            ]);
        }, ARRAY_FILTER_USE_KEY);

        if (! empty($headers)) {
            $code->addLine('Headers =', 1);
            $code->addLine('{', 1);

            foreach ($headers as $key => $values) {
                $key = escapeForDoubleQuotes($key);
                $value = escapeForDoubleQuotes(implode(', ', $values));
                $code->addLine(sprintf('{"%s", "%s"},', $key, $value), 2);
            }

            $code->addLine('},', 1);
        }

        if (strlen($body) > 0) {
            if ($contentType === ContentType::FORM) {
                $code->addLine('Content = new FormUrlEncodedContent(new Dictionary<string, string>)', 1);
                $code->addLine('{', 1);
                $body = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
                foreach ($body as $key => $value) {
                    $code->addLine(sprintf('{"%s", "%s"},', $key, escapeForDoubleQuotes($value)), 2);
                }
                $code->addLine('}),', 1);
            } else {
                $code->addLine(sprintf('Content = new StringContent("%s"),', escapeForDoubleQuotes($body)), 1);
            }
        }

        $code->addLine('};');
        $code->addLine('');
        $code->addLine('using (var response = await client.SendAsync(request))');
        $code->addLine('{');
        $code->addLine('response.EnsureSuccessStatusCode();', 1);
        $code->addLine('var body = await response.Content.ReadAsStringAsync();', 1);
        $code->addLine('Console.WriteLine(body);', 1);
        $code->addLine('}');

        return $code;
    }
}
