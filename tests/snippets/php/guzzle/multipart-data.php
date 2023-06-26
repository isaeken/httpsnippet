<?php

$client = new \GuzzleHttp\Client();

$response = $client->request(
    'POST',
    'http://mockbin.com/har',
    [
        'multipart' => [
            [
                'name' => 'foo',
                'contents' => 'Hello World',
                'filename' => 'hello.txt',
            ],
            [
                'name' => 'bar',
                'contents' => 'Bonjour le monde',
            ],
        ],
        'headers' => [
            "Content-Type" => "multipart/form-data",
        ],
    ],
);

echo $response->getBody();
