<?php

$client = new \GuzzleHttp\Client();

$response = $client->request(
    'GET',
    'http://mockbin.com/har?bar=bar&baz=baz&key=value',
);

echo $response->getBody();
