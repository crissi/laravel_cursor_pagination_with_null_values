<?php
namespace Crissi\LaravelCursorPaginationWithNullValues;

use Illuminate\Pagination\Cursor;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\CursorPaginator;
use Crissi\LaravelCursorPaginationWithNullValues\CursorPaginateWithNullValues;

class NullValueCursorPagination
{
    public function implementation()
    {
        return function ($perPage, $columns = ['*'], $cursorName = 'cursor', $cursor = null) {
            if (!$cursor instanceof Cursor) {
                $cursor = is_string($cursor)
                    ? Cursor::fromEncoded($cursor)
                    : CursorPaginator::resolveCurrentCursor($cursorName, $cursor);
            }

            $orders = $this->ensureOrderForCursorPagination(null !== $cursor && $cursor->pointsToPreviousItems());

            if (null !== $cursor) {
                $addCursorConditions = function (self $builder, $previousColumn, $i) use (&$addCursorConditions, $cursor, $orders) {
                    if (null !== $previousColumn) {
                        /** @var Builder $builder */
                        $builder->where(
                            $this->getOriginalColumnNameForCursorPagination($this, $previousColumn),
                            '=',
                            $cursor->parameter($previousColumn)
                        );
                    }

                    $builder->where(function (self $builder) use ($addCursorConditions, $cursor, $orders, $i) {
                        ['column' => $column, 'direction' => $direction] = $orders[$i];

                        $param = $cursor->parameter($column);
                        $originalColumn = $this->getOriginalColumnNameForCursorPagination($this, $column);

                        if (null === $param) {
                            (new CursorPaginateWithNullValues())->whereClauseForCursorNullValue($originalColumn, $builder, $direction);
                        } else {
                            $builder->where(function ($builder) use ($originalColumn, $direction, $param) {
                                $builder->where(
                                    $originalColumn,
                                    $direction === 'asc' ? '>' : '<',
                                    $param
                                );

                                if ($direction === 'desc') {
                                    $builder->orWhereNull($originalColumn);
                                }
                            });
                        }

                        if ($i < $orders->count() - 1) {
                            $builder->orWhere(function (self $builder) use ($addCursorConditions, $column, $i) {
                                $addCursorConditions($builder, $column, $i + 1);
                            });
                        }
                    });
                };

                $addCursorConditions($this, null, 0);
            }

            $this->limit($perPage + 1);

            return $this->cursorPaginator($this->get($columns), $perPage, $cursor, [
                'path' => Paginator::resolveCurrentPath(),
                'cursorName' => $cursorName,
                'parameters' => $orders->pluck('column')->toArray(),
            ]);
        };
    }
}
