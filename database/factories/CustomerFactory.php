<?php

namespace Crissi\LaravelCursorPaginationWithNullValues\Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Crissi\LaravelCursorPaginationWithNullValues\Database\Models\Customer;


class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition()
    {
        return [
            'name' => Str::random(),
            'email' => Str::random()
        ];
    }
}

