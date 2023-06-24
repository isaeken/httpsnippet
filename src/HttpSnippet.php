<?php

namespace IsaEken\HttpSnippet;

use Illuminate\Support\Arr;
use IsaEken\HttpSnippet\Contracts\Language;
use IsaEken\HttpSnippet\Exceptions\LanguageNotFoundException;
use IsaEken\HttpSnippet\Traits\HasLanguage;
use IsaEken\HttpSnippet\Traits\HasRequest;
use IsaEken\HttpSnippet\Languages;
use Psr\Http\Message\RequestInterface;

class HttpSnippet
{
    use HasLanguage;
    use HasRequest;

    public array $languages = [
        'c.libcurl' => Languages\C\LibCurl::class,
        'csharp.httpclient' => Languages\CSharp\HttpClient::class,
        'csharp.restsharp' => Languages\CSharp\RestSharp::class,
        'php.curl' => Languages\Php\Curl::class,
        'shell.curl' => Languages\Shell\Curl::class,
        'shell.wget' => Languages\Shell\Wget::class,
    ];

    /**
     * @return array<array{name: string, title: string, link: string, description: string}>
     */
    public function getAvailableLanguages(): array
    {
        return Arr::map(array_values($this->languages), fn ($language) => $language::info());
    }

    public function getLanguages(): array
    {
        return $this->languages;
    }

    public function setLanguages(array $languages): self
    {
        return tap($this, function () use ($languages) {
            $this->languages = $languages;
        });
    }

    public function registerLanguage(string $class): self
    {
        return tap($this, function () use ($class) {
            $name = getHttpSnippetLanguageName($class);
            $this->languages[$name] = $class;
        });
    }

    public function unregisterLanguage(string $classOrName): self
    {
        return tap($this, function () use ($classOrName) {
            if (array_key_exists($classOrName, $this->languages)) {
                unset($this->languages[$classOrName]);
                return;
            }

            $name = getHttpSnippetLanguageName($classOrName);
            unset($this->languages[$name]);
        });
    }

    public static function make(RequestInterface $request, string $language): Language
    {
        $instance = new static();
        $instance->useRequest($request);

        if (! array_key_exists($language, $instance->getLanguages())) {
            throw new LanguageNotFoundException($language);
        }

        $language = new ($instance->getLanguages()[$language]);

        /** @var Language $language */
        return $language->useHttpSnippet($instance);
    }
}
