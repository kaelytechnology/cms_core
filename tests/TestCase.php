<?php

namespace Kaely\CmsCore\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Kaely\CmsCore\CmsCoreServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            CmsCoreServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Configurar base de datos de prueba
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Configurar cache
        $app['config']->set('cache.default', 'array');

        // Configurar CMS Core
        $app['config']->set('cms-core.modules.type', 'CMS');
        $app['config']->set('cms-core.auth_integration.enabled', false);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Ejecutar migraciones
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->artisan('migrate');
    }
} 