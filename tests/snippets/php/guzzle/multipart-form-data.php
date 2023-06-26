<?php

$client = new \GuzzleHttp\Client();

$response = $client->request(
    'POST',
    'http://mockbin.com/har',
    [
        'multipart' => [
            [
                'name' => 'foo',
                'contents' => 'bar',
            ],
        ],
        'headers' => [
            "Content-Type" => "multipart/form-data",
        ],
    ],
);

echo $response->getBody();
