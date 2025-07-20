<?php

namespace Kaely\CmsCore\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallCmsCore extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cms:install 
                            {--force : Forzar la instalación sin confirmación}
                            {--skip-migrations : Omitir la ejecución de migraciones}';

    /**
     * The console command description.
     */
    protected $description = 'Instalar el paquete CMS Core';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Instalando CMS Core...');

        if (!$this->option('force') && !$this->confirm('¿Desea continuar con la instalación?')) {
            $this->info('Instalación cancelada.');
            return 0;
        }

        try {
            // 1. Verificar dependencia de auth-package
            $this->info('🔍 Verificando dependencias...');
            if (!class_exists('\Kaely\AuthPackage\Models\Module')) {
                $this->error('❌ El paquete auth-package no está instalado. CMS Core requiere auth-package.');
                return 1;
            }

            // 2. Publicar configuraciones
            $this->info('📋 Publicando configuraciones...');
            $this->call('vendor:publish', [
                '--tag' => 'cms-core-config',
                '--force' => true,
            ]);

            // 3. Ejecutar migraciones
            if (!$this->option('skip-migrations')) {
                $this->info('🗄️ Ejecutando migraciones...');
                $this->call('migrate', [
                    '--path' => 'vendor/kaelytechnology/cms-core/database/migrations',
                    '--force' => true,
                ]);
            }

            // 4. Registrar módulo CMS Core
            $this->info('📦 Registrando módulo CMS Core...');
            $this->registerCmsModule();

            $this->info('✅ CMS Core instalado correctamente!');
            $this->info('');
            $this->info('📖 Próximos pasos:');
            $this->info('   - Ejecute: php artisan cms:register-module para registrar módulos CMS');
            $this->info('   - Consulte la documentación para más información');
            $this->info('   - Configure las variables de entorno según sea necesario');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error durante la instalación: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Registrar el módulo CMS Core
     */
    protected function registerCmsModule(): void
    {
        try {
            $module = \Kaely\AuthPackage\Models\Module::updateOrCreate(
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

            $this->info("✅ Módulo CMS Core registrado con ID: {$module->id}");

        } catch (\Exception $e) {
            $this->error('❌ Error al registrar módulo CMS Core: ' . $e->getMessage());
        }
    }
} 