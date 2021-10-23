# Laravel cursor pagination with null values

[![Latest Version on Packagist](https://img.shields.io/packagist/v/crissi/laravel-cursor-pagination-with-null-values.svg?style=flat-square)](https://packagist.org/packages/crissi/laravel-cursor-pagination-with-null-values)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/crissi/laravel-cursor-pagination-with-null-values/run-tests?label=tests)](https://github.com/crissi/laravel-cursor-pagination-with-null-values/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/crissi/laravel-cursor-pagination-with-null-values/Check%20&%20fix%20styling?label=code%20style)](https://github.com/crissi/laravel-cursor-pagination-with-null-values/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/crissi/laravel-cursor-pagination-with-null-values.svg?style=flat-square)](https://packagist.org/packages/crissi/laravel-cursor-pagination-with-null-values)

---
Adds a new method to allow cursor pagination to work with column that can have null values using the same method argument signature as Laravel's current **cursorPaginate**-method. 

Why?
Columns with null values are not supported in the current Laravel implementation as mentioned in Laravel's docs.

https://laravel.com/docs/8.x/pagination#cursor-vs-offset-pagination

Currently works for database drivers: **sqlite**, **mysql** and **sqlserver**

---

## Installation

You can install the package via composer:

```bash
composer require crissi/laravel-cursor-pagination-with-null-values
```


You can publish the config file with:
```bash
php artisan vendor:publish --provider="Crissi\LaravelCursorPaginationWithNullValues\LaravelCursorPaginationWithNullValuesServiceProvider" --tag="laravel-cursor-pagination-with-null-values-config"
```

This is the contents of the published config file:

```php
return [
    'method_name' => 'cursorPaginateWithNullValues'
];;
```

## Usage

Before: 
```php
return User::orderBy('id')->cursorPaginate(5);
```
After:
```php
return User::orderBy('id')->cursorPaginateWithNullValues(5);
```
```php
return User::orderBy('nullable_column')->orderBy('id')->cursorPaginateWithNullValues(5);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Credits

- [Christian Nielsen](https://github.com/crissi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
