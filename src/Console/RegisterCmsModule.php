<?php

namespace Kaely\CmsCore\Console;

use Illuminate\Console\Command;
use Kaely\AuthPackage\Models\Module;

class RegisterCmsModule extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cms:register-module 
                            {name : Nombre del módulo}
                            {--slug= : Slug del módulo (opcional, se genera automáticamente)}
                            {--description= : Descripción del módulo}
                            {--icon= : Icono del módulo (FontAwesome)}
                            {--route= : Ruta del módulo}
                            {--order=1 : Orden del módulo}
                            {--force : Forzar el registro sin confirmación}';

    /**
     * The console command description.
     */
    protected $description = 'Registrar un módulo CMS en auth-package';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = $this->argument('name');
        $slug = $this->option('slug') ?: $this->generateSlug($moduleName);

        // Verificar si el módulo ya existe
        $existingModule = Module::where('slug', $slug)->first();
        if ($existingModule) {
            if (!$this->option('force') && !$this->confirm("El módulo con slug '{$slug}' ya existe. ¿Desea actualizarlo?")) {
                $this->info('Registro cancelado.');
                return 0;
            }
        }

        // Recopilar datos del módulo
        $moduleData = [
            'name' => $moduleName,
            'slug' => $slug,
            'type' => 'CMS',
            'description' => $this->option('description') ?: "Módulo CMS: {$moduleName}",
            'icon' => $this->option('icon') ?: 'fas fa-cube',
            'route' => $this->option('route') ?: "/api/cms/{$slug}",
            'order' => (int) $this->option('order'),
            'is_active' => true,
        ];

        // Mostrar resumen
        $this->info('📦 Registrando módulo CMS:');
        $this->table(
            ['Campo', 'Valor'],
            [
                ['Nombre', $moduleData['name']],
                ['Slug', $moduleData['slug']],
                ['Tipo', $moduleData['type']],
                ['Descripción', $moduleData['description']],
                ['Icono', $moduleData['icon']],
                ['Ruta', $moduleData['route']],
                ['Orden', $moduleData['order']],
            ]
        );

        if (!$this->option('force') && !$this->confirm('¿Desea continuar con el registro?')) {
            $this->info('Registro cancelado.');
            return 0;
        }

        try {
            // Registrar el módulo
            $module = Module::updateOrCreate(
                ['slug' => $slug],
                $moduleData
            );

            $this->info("✅ Módulo CMS '{$moduleName}' registrado correctamente con ID: {$module->id}");
            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error durante el registro: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Generar slug automáticamente
     */
    protected function generateSlug(string $name): string
    {
        return strtolower(
            preg_replace('/[^a-zA-Z0-9]+/', '-', $name)
        );
    }
} 