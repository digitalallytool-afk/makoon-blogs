<?php

namespace Tests;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Boot the application, refusing to continue unless it points at the throwaway in-memory database.
     *
     * Feature tests use RefreshDatabase, which drops every table. This runs before any test
     * trait touches the database so a stale config cache or a leaked environment variable
     * can never aim the test suite at the real application database.
     */
    public function createApplication(): Application
    {
        $app = parent::createApplication();

        $connection = $app['config']->get('database.default');
        $database = $app['config']->get("database.connections.{$connection}.database");

        if ($app->environment() !== 'testing' || $connection !== 'sqlite' || $database !== ':memory:') {
            throw new RuntimeException(
                'Refusing to run tests: they must use the in-memory sqlite database, but the app is configured with '
                ."environment [{$app->environment()}], connection [{$connection}], database [{$database}]. "
                .'This usually means a config cache exists. Run `php artisan optimize:clear` and try again. '
                .'No database was touched.'
            );
        }

        return $app;
    }
}
