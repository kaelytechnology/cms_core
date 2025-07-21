<?php

namespace Kaely\CmsCore;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class CmsCoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar configuraciones
        $this->mergeConfigFrom(__DIR__.'/../config/cms-core.php', 'cms-core');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publicar .env.example
        $this->publishes([
            __DIR__.'/../.env.example' => base_path('vendor/kaelytechnology/cms-core/.env.example'),
        ], 'cms-core-env');
        // Configurar Schema para MySQL
        Schema::defaultStringLength(191);

        // Publicar configuraciones
        $this->publishes([
            __DIR__.'/../config/cms-core.php' => config_path('cms-core.php'),
        ], 'cms-core-config');

        // Publicar migraciones
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'cms-core-migrations');

        // Cargar rutas
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        // Cargar migraciones
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Registrar comandos
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Kaely\CmsCore\Console\InstallCmsCore::class,
                \Kaely\CmsCore\Console\RegisterCmsModule::class,
                \Kaely\CmsCore\Console\CopyEnvExample::class,
            ]);
        }

        // Registrar módulo CMS Core en el menú dinámico
        $this->registerCmsModule();
    }

    /**
     * Registrar el módulo CMS Core en auth-package
     */
    protected function registerCmsModule(): void
    {
        // Solo registrar si auth-package está disponible
        if (!class_exists('\Kaely\AuthPackage\Models\Module')) {
            return;
        }

        try {
            \Kaely\AuthPackage\Models\Module::updateOrCreate(
                ['slug' => 'cms-core'],
                [
                    'name' => 'CMS Core',
                    'type' => 'CMS',
                    'description' => 'Núcleo del sistema CMS modular',
                    'icon' => 'fas fa-cogs',
                    'route' => '/api/cms/modules',
                    'order' => 1,
                    'is_active' => true,
                ]
            );
        } catch (\Exception $e) {
            // Log error pero no fallar la aplicación
            \Log::warning('Error al registrar módulo CMS Core: ' . $e->getMessage());
        }
    }
} 