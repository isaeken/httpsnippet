<?php

$client = new \GuzzleHttp\Client();

$response = $client->request(
    'POST',
    'http://mockbin.com/har',
    [
        'headers' => [
            "Cookie" => "foo=bar; bar=baz",
        ],
    ],
);

echo $response->getBody();
