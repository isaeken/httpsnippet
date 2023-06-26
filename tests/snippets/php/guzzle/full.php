<?php

$client = new \GuzzleHttp\Client();

$response = $client->request(
    'POST',
    'http://mockbin.com/har?foo=bar&bar=baz&baz=abc&key=value',
    [
        'form_params' => [
            'foo' => 'bar',
        ],
        'headers' => [
            "Accept" => "application/json",
            "Content-Type" => "application/x-www-form-urlencoded",
            "Cookie" => "foo=bar; bar=baz",
        ],
    ],
);

echo $response->getBody();
