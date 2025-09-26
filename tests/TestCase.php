<?php
namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use WithFaker;

    protected function getPackageProviders($app)
    {
        return [
            // Providers required for features; in a full app these are loaded via config/app
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        // run migrations for the in-memory sqlite
        $this->loadLaravelMigrations();
        $this->artisan('migrate', ['--database' => 'sqlite'])->run();
    }
}
