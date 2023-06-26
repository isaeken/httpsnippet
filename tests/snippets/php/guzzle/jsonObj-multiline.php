<?php

$client = new \GuzzleHttp\Client();

$response = $client->request(
    'POST',
    'http://mockbin.com/har',
    [
        'body' => '{
    "foo": "bar"
}',
        'headers' => [
            "Content-Type" => "application/json",
        ],
    ],
);

echo $response->getBody();
