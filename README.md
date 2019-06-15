# Laravel Test Suite

[![pipeline status](https://gitlab.com/two-thirds/laravel-test-suite/badges/master/pipeline.svg)](https://gitlab.com/two-thirds/laravel-test-suite/commits/master)
[![coverage report](https://gitlab.com/two-thirds/laravel-test-suite/badges/master/coverage.svg)](https://gitlab.com/two-thirds/laravel-test-suite/commits/master)

Add Laravel Test Suite to your project to make it easy to run your testing tools directly from Artisan. Once installed, just run `php artisan test`. Out of the box it automatically detects and runs [phpunit](https://phpunit.de/) ( with or without code coverage ), [Laravel dusk](https://github.com/laravel/dusk), [php-cs-fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) and [php mess detector](https://phpmd.org/).

I welcome any additional tools or bugfixes via pull / merge request.

<!-- MarkdownTOC autolink="true" autoanchor="true" bracket="round" -->

- [Installation](#installation)
	- [Composer](#composer)
	- [Register service provider](#register-service-provider)
	- [Publishing config \(optional\)](#publishing-config-optional)
	- [Test your code](#test-your-code)

<!-- /MarkdownTOC -->

<a id="installation"></a>
# Installation

<a id="composer"></a>
## Composer

Laravel Test Suite can be installed through composer:

    composer require --dev two-thirds/laravel-test-suite

<a id="register-service-provider"></a>
## Register service provider

If you are using Laravel 5.5 or greater, the service provider will be loaded automatically. If using 5.4, add the following to the `providers` array of `config/app.php`:

```
        TwoThirds\TestSuite\TestSuiteServiceProvider::class,
```

<a id="publishing-config-optional"></a>
## Publishing config (optional)

Laravel Test Suite comes with a sane set of defaults out of the box but if you want to customize how any of the suites are ran, you can publish to your project:

    php artisan vendor:publish --tag=laravel-test-suite

<a id="test-your-code"></a>
## Test your code

Simply run `php artisan test` to run the whole test suite against your codebase.

Tests can be run individually as well:

	php artisan test:phpunit
	php artisan test:dusk
	php artisan test:php-cs-fixer
	php artisan test:phpmd
