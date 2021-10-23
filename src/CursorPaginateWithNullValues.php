<?php

namespace Crissi\LaravelCursorPaginationWithNullValues;

class CursorPaginateWithNullValues
{
    /**
     * Handle creating a where clause for when a cursor value is null.
     *
     * @param  string  $column
     * @param  \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder  $builder
     * @param  string  $direction
     *
     * @return void
     */
    public function whereClauseForCursorNullValue($column, $builder, $direction)
    {
        $ascending = $direction === 'asc';

        if ($ascending) {
            $builder->whereNotNull($column);
        }
    }

    /**
     * Nulls are ordered first when ordring in ascending order.
     *
     * @return bool
     */
    public function nullsAreOrderedFirst()
    {
        return true;
    }
}
