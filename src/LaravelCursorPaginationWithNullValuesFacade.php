<?php

namespace Crissi\LaravelCursorPaginationWithNullValues;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Crissi\LaravelCursorPaginationWithNullValues\LaravelCursorPaginationWithNullValues
 */
class LaravelCursorPaginationWithNullValuesFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-cursor-pagination-with-null-values';
    }
}
