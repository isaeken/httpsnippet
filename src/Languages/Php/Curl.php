<?php

namespace IsaEken\HttpSnippet\Languages\Php;

use IsaEken\HttpSnippet\Abstracts\AbstractLanguage;
use IsaEken\HttpSnippet\CodeGenerator;
use IsaEken\HttpSnippet\Contracts\Language;
use JetBrains\PhpStorm\ArrayShape;

class Curl extends AbstractLanguage implements Language
{
    public static string|null $name = 'php.curl';
    public static string|null $title = 'PHP cURL';
    public static string|null $link = 'https://www.php.net/manual/en/book.curl.php';
    public static string|null $description = 'PHP with ext-curl.';

    public function make(): CodeGenerator
    {
        $code = new CodeGenerator();
        $request = $this->getHttpSnippet()->getRequest();
        $port = $request->getUri()->getPort();
        $url = (string) $request->getUri();
        $method = $request->getMethod();
        $body = $request->getBody()->getContents();
        $headers = $request->getHeaders();
        $cookies = $request->getCookies();

        $code->addLines([
            '$curl = curl_init();',
            sprintf('curl_setopt($curl, CURLOPT_PORT, %s);', $port),
            sprintf('curl_setopt($curl, CURLOPT_URL, "%s");', $url),
            'curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);',
            'curl_setopt($curl, CURLOPT_ENCODING, "");',
            'curl_setopt($curl, CURLOPT_MAXREDIRS, 10);',
            'curl_setopt($curl, CURLOPT_TIMEOUT, 30);',
            'curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);',
            sprintf('curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "%s");', $method),
        ]);

        if (strlen($body) > 0) {
            if ($request->isJson()) {
                $code->addLine('curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([');
                $body = json_decode($body, true);
                foreach ($body as $key => $value) {
                    $code->addLine(sprintf('"%s" => "%s",', escapeForDoubleQuotes($key), escapeForDoubleQuotes($value)), 1);
                }
                $code->addLine(']));');
            } else {
                $code->addLine(sprintf('curl_setopt($curl, CURLOPT_POSTFIELDS, "%s");', escapeForDoubleQuotes($body)));
            }
        }

        if (count($headers) > 0) {
            $code->addEmptyLine();
            $code->addLine('curl_setopt($curl, CURLOPT_HTTPHEADER, [');
            foreach ($headers as $key => $value) {
                $header = sprintf('%s: %s', escapeForDoubleQuotes($key), escapeForDoubleQuotes($value[0]));
                $code->addLine(sprintf('"%s",', $header), 1);
            }
            $code->addLine(']);');
        }

        if (count($cookies) > 0) {
            $code->addEmptyLine();

            foreach ($cookies as $cookie) {
                $name = urlencode($cookie->getName());
                $value = urlencode($cookie->getValue());
                $cookie = sprintf('%s=%s', $name, $value);
                $code->addLine(sprintf('curl_setopt($curl, CURLOPT_COOKIE, "%s");', $cookie));
            }
        }

        $code->addEmptyLine();
        $code->addLine('$response = curl_exec($curl);');
        $code->addLine('$err = curl_error($curl);');
        $code->addLine('$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);');
        $code->addLine('curl_close($curl);');
        $code->addEmptyLine();
        $code->addLine('if ($err) {');
        $code->addLine('echo "cURL Error #:" . $err;', 1);
        $code->addLine('} else {');
        $code->addLine('echo $response;', 1);
        $code->addLine('}');

        return $code;
    }
}
