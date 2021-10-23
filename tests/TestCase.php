<?php

namespace Crissi\LaravelCursorPaginationWithNullValues\Tests;

use Crissi\LaravelCursorPaginationWithNullValues\LaravelCursorPaginationWithNullValuesServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Crissi\\LaravelCursorPaginationWithNullValues\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelCursorPaginationWithNullValuesServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/../database/migrations/2021_10_18_192048_customer.php';
        $migration->up();
    }
}
