<?php

namespace Crissi\LaravelCursorPaginationWithNullValues;

use Spatie\LaravelPackageTools\Package;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Crissi\LaravelCursorPaginationWithNullValues\Commands\LaravelCursorPaginationWithNullValuesCommand;

class LaravelCursorPaginationWithNullValuesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-cursor-pagination-with-null-values')
            ->hasConfigFile();
    }

    public function bootingPackage()
    {
        $methodName = config('cursor-pagination-with-null-values.method_name');

        QueryBuilder::macro($methodName, (new NullValueCursorPagination)->implementation());
        EloquentBuilder::macro($methodName, (new NullValueCursorPagination)->implementation());
    }
}
