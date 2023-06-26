<?php

$client = new \GuzzleHttp\Client();

$response = $client->request(
    'GET',
    'http://mockbin.com/har',
    [
        'headers' => [
            "Accept" => "application/json",
            "Quoted-Value" => "\"quoted\" 'string'",
            "X-Foo" => "Bar",
        ],
    ],
);

echo $response->getBody();
