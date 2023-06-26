# httpsnippet

[![Latest Version on Packagist](https://img.shields.io/packagist/v/isaeken/httpsnippet.svg?style=flat-square)](https://packagist.org/packages/isaeken/httpsnippet)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/isaeken/httpsnippet/run-tests?label=tests)](https://github.com/isaeken/httpsnippet/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/isaeken/httpsnippet.svg?style=flat-square)](https://packagist.org/packages/isaeken/httpsnippet)

`httpsnippet` is a PHP package that enables you to make HTTP request codes for any language. This can be particularly useful for developers who are looking to perform HTTP requests using various programming languages.

## Example

Here’s how you can create a GET request code in PHP:

```php
$request = new \IsaEken\HttpSnippet\Request(
    method: 'GET',
    uri: 'https://example.com'
);

echo \IsaEken\HttpSnippet\HttpSnippet::make($request, 'php.curl')->toString();
```

## Installation

You can install this package via composer:

```bash
composer require isaeken/httpsnippet
```

## Usage

The `httpsnippet` package allows you to create HTTP request codes in various programming languages. Below are the steps to use this package:

### 1. Import the Package

Before you start, make sure that you have installed the package via composer. If not, you can install it by running:

```sh
composer require isaeken/httpsnippet
```

Now, import the httpsnippet package in your PHP script:

```php
use IsaEken\HttpSnippet\HttpSnippet;
use IsaEken\HttpSnippet\Request;

require_once __DIR__ . '/vendor/autoload.php';
```

### 2. Create a Request

Next, create a request object. You can do this by passing the request method, URI, headers, and body to the `Request` class:

```php
$request = new Request(
    method: 'GET',
    uri: 'https://example.com',
    headers: [
        'Accept' => 'application/json',
    ],
    body: [
        'foo' => 'bar',
    ],
    version: '1.1',
    cookies: [
        'foo' => 'bar',
    ],
);
```

### 3. Generate the Request Code

Now, use the make method of the HttpSnippet class to generate the request code for the desired programming language. Pass in the request object and the target programming language as parameters.

```php
$code = HttpSnippet::make($request, 'language.identifier');
```

For instance, to generate the code in PHP using cURL, you would do:

```php
$code = HttpSnippet::make($request, 'php.curl');
```

### 4. Output or Use the Code

You can now output or use the generated code as needed. For example, to print the code:

```php
echo $code->toString();
```

Or, to save the code to a file:

```php
file_put_contents('code.txt', $code->toString());
```

## Supported Languages and Identifiers

The following languages and identifiers are supported:

| Language | Identifier          | Ready for use? |
| --- |----------------------------|----------------|
| C | `libcurl`           | ❌              |
| C# | `csharp.httpclient` | ❌               |
| C# | `csharp.restsharp`  | ❌               |
| PHP | `php.curl`          | ❌               |
| PHP | `php.guzzle`        | ✅              |
| Shell | `shell.curl`        | ❌              |
| Shell | `shell.wget`        | ❌              |

## Testing

Run the tests with:

```bash
composer test
```

## Changelog

All notable changes to `httpsnippet` will be documented in this file

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email hello@isaeken.com.tr instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
