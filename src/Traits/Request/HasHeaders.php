<?php

namespace IsaEken\HttpSnippet\Traits\Request;

use IsaEken\HttpSnippet\Enums\ContentType;

trait HasHeaders
{
    public array $headers = [];

    public array $filteredHeaders = [
        'connection',
        'content-length',
        'date',
        'expect',
        'host',
        'keep-alive',
        'proxy-connection',
        'te',
        'trailer',
        'transfer-encoding',
        'upgrade',
    ];

    public function hasHeader(string $name): bool
    {
        $name = strtolower($name);
        return array_key_exists($name, $this->headers);
    }

    public function getHeader(string $name): array
    {
        $name = strtolower($name);
        return $this->headers[$name] ?? [];
    }

    public function getHeaderLine(string $name): string
    {
        $name = strtolower($name);
        return implode('; ', $this->headers[$name] ?? []);
    }

    public function withHeader(string $name, $value): self
    {
        $name = strtolower($name);
        $value = is_array($value) ? $value : [$value];
        $value = array_map(fn ($value) => trim($value), $value);
        $value = array_filter($value, fn ($value) => ! empty($value));
        $value = array_values($value);

        return tap($this, fn (self $request) => $request->headers[$name] = $value);
    }

    public function withAddedHeader(string $name, $value): self
    {
        $name = strtolower($name);
        $value = is_array($value) ? $value : [$value];
        $value = array_map(fn ($value) => trim($value), $value);
        $value = array_filter($value, fn ($value) => ! empty($value));
        $value = array_values($value);

        return tap($this, fn (self $request) => $request->headers[$name] = array_merge($request->headers[$name] ?? [], $value));
    }

    public function withoutHeader(string $name): self
    {
        $name = strtolower($name);

        return tap($this, function (self $request) use ($name) {
            unset($request->headers[$name]);
        });
    }

    public function getHeaders(bool $filtered = true): array
    {
        if ($filtered) {
            return array_filter($this->headers, fn ($name) => ! in_array($name, $this->filteredHeaders));
        }

        return $this->headers;
    }

    public function withHeaders(array $headers): self
    {
        return tap($this, function (self $request) use ($headers) {
            foreach ($headers as $name => $value) {
                $request->withHeader($name, $value);
            }
        });
    }

    public function getContentType(): ContentType
    {
        $contentType = $this->getHeaderLine('content-type');

        if (str_contains($contentType, 'application/json')) {
            return ContentType::JSON;
        }

        if (str_contains($contentType, 'application/x-www-form-urlencoded')) {
            return ContentType::FORM;
        }

        if (str_contains($contentType, 'multipart/form-data')) {
            return ContentType::MULTIPART;
        }

        return ContentType::RAW;
    }

    public function isJson(): bool
    {
        return $this->getContentType() === ContentType::JSON;
    }
}
