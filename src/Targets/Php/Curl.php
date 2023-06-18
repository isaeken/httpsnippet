<?php

namespace IsaEken\HttpSnippet\Targets\Php;

use IsaEken\HttpSnippet\CodeGenerator;
use IsaEken\HttpSnippet\Contracts\Target;
use IsaEken\HttpSnippet\Targets\AbstractTarget;
use JetBrains\PhpStorm\ArrayShape;

class Curl extends AbstractTarget implements Target
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
            'name' => 'php.curl',
            'title' => 'cURL',
            'link' => 'https://www.php.net/manual/en/book.curl.php',
            'description' => 'PHP with ext-curl.',
        ];
    }

    public function make(): CodeGenerator
    {
        $code = new CodeGenerator();
        $port = $this->getHttpSnippet()->getRequest()->getUri()->getPort();
        $url = (string) $this->getHttpSnippet()->getRequest()->getUri();
        $method = $this->getHttpSnippet()->getRequest()->getMethod();
        $body = $this->getHttpSnippet()->getRequest()->getBody()->getContents();
        $headers = $this->getHttpSnippet()->getRequest()->getHeaders();
        $cookies = $this->getHttpSnippet()->getCookies();

        $code->addLine('$curl = curl_init();');

        $code->addLine(sprintf('curl_setopt($curl, CURLOPT_PORT, %s);', $port));
        $code->addLine(sprintf('curl_setopt($curl, CURLOPT_URL, "%s");', $url));
        $code->addLine('curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);');
        $code->addLine('curl_setopt($curl, CURLOPT_ENCODING, "");');
        $code->addLine('curl_setopt($curl, CURLOPT_MAXREDIRS, 10);');
        $code->addLine('curl_setopt($curl, CURLOPT_TIMEOUT, 30);');
        $code->addLine('curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);');
        $code->addLine(sprintf('curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "%s");', $method));

        if (strlen($body) > 0) {
            if ($this->getHttpSnippet()->isJson()) {
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
            $code->addLine('');
            $code->addLine('curl_setopt($curl, CURLOPT_HTTPHEADER, [');
            foreach ($headers as $key => $value) {
                $header = sprintf('%s: %s', escapeForDoubleQuotes($key), escapeForDoubleQuotes($value[0]));
                $code->addLine(sprintf('"%s",', $header), 1);
            }
            $code->addLine(']);');
        }

        if (count($cookies) > 0) {
            $code->addLine('');

            foreach ($cookies as $cookie) {
                $name = urlencode($cookie->getName());
                $value = urlencode($cookie->getValue());
                $cookie = sprintf('%s=%s', $name, $value);
                $code->addLine(sprintf('curl_setopt($curl, CURLOPT_COOKIE, "%s");', $cookie));
            }
        }

        $code->addLine('');
        $code->addLine('$response = curl_exec($curl);');
        $code->addLine('$err = curl_error($curl);');
        $code->addLine('$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);');
        $code->addLine('curl_close($curl);');
        $code->addLine('');
        $code->addLine('if ($err) {');
        $code->addLine('echo "cURL Error #:" . $err;', 1);
        $code->addLine('} else {');
        $code->addLine('echo $response;', 1);
        $code->addLine('}');

        return $code;
    }
}
