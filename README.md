# Make HTTP request codes for any language

[![Latest Version on Packagist](https://img.shields.io/packagist/v/isaeken/httpsnippet.svg?style=flat-square)](https://packagist.org/packages/isaeken/httpsnippet)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/isaeken/httpsnippet/run-tests?label=tests)](https://github.com/isaeken/httpsnippet/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/isaeken/httpsnippet.svg?style=flat-square)](https://packagist.org/packages/isaeken/httpsnippet)

You can create GET request code like this:

```php
$request = new \GuzzleHttp\Psr7\Request('GET', 'https://example.com');
return \IsaEken\HttpSnippet\HttpSnippet::make($request, 'php.curl');
```

# Installation

You can install the package via composer:

```bash
composer require isaeken/httpsnippet
```

# Usage

```php
$request = new \GuzzleHttp\Psr7\Request('GET', 'https://example.com');
return \IsaEken\HttpSnippet\HttpSnippet::make($request, 'php.curl');
```

# Testing

```bash
composer test
```

# Changelog

All notable changes to `httpsnippet` will be documented in this file

# Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

# Security

If you discover any security related issues, please email hello@isaeken.com.tr instead of using the issue tracker.
