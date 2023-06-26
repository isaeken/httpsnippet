<?php

$client = new \GuzzleHttp\Client();

$response = $client->request(
    'POST',
    'http://mockbin.com/har',
    [
        'body' => 'Hello World',
        'headers' => [
            "Content-Type" => "text/plain",
        ],
    ],
);

echo $response->getBody();
