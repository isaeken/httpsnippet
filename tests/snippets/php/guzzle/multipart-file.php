<?php

$client = new \GuzzleHttp\Client();

$response = $client->request(
    'POST',
    'http://mockbin.com/har',
    [
        'multipart' => [
            [
                'name' => 'foo',
                'contents' => fopen('test/fixtures/files/hello.txt', 'r'),
                'filename' => 'test/fixtures/files/hello.txt',
            ],
        ],
        'headers' => [
            "Content-Type" => "multipart/form-data",
        ],
    ],
);

echo $response->getBody();
