<?php

namespace IsaEken\HttpSnippet\Traits\Request;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Psr\Http\Message\StreamInterface;

trait HasBody
{
    public StreamInterface|null $body = null;

    public function getBody(): StreamInterface
    {
        return $this->body ?? Utils::streamFor();
    }

    public function withBody(mixed $body): self
    {
        return tap($this, function (self $request) use ($body) {
            if ($body instanceof StreamInterface) {
                $request->body = $body;
                return;
            } elseif ($body instanceof Jsonable) {
                $body = $body->toJson();
            } elseif ($body instanceof Arrayable) {
                $body = json_encode($body->toArray());
            } elseif (is_array($body)) {
                $body = json_encode($body);
            }

            $request->body = Utils::streamFor($body);
        });
    }

    public function getMultipartData(): array
    {
        $body = json_decode($this->getBody()->getContents(), true);
        $data = [];

        if (is_null($body)) {
            return [];
        }

        foreach ($body as $object) {
            $data[] = [
                'name' => $object['name'] ?? false,
                'contents' => $object['contents'] ?? false,
                'file' => $object['file'] ?? false,
                'filename' => $object['filename'] ?? false,
            ];
        }

        return $data;
    }

    public function getFormData(): array
    {
        $body = json_decode($this->getBody()->getContents(), true);
        $data = [];

        if (is_null($body)) {
            return [];
        }

        foreach ($body as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
