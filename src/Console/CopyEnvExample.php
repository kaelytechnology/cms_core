<?php

namespace Kaely\CmsCore\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CopyEnvExample extends Command
{
    protected $signature = 'cms-core:copy-env';
    protected $description = 'Copia el archivo .env.example a .env si no existe';

    public function handle()
    {
        $filesystem = new Filesystem();
        $envPath = base_path('.env');
        $examplePath = base_path('vendor/kaelytechnology/cms-core/.env.example');

        if ($filesystem->exists($examplePath)) {
            $envVars = $filesystem->get($examplePath);
            $envVars = "# === Variables de entorno para cms-core (inicio) ===\n"
                . $envVars . "\n# === Variables de entorno para cms-core (fin) ===\n";
            if (!$filesystem->exists($envPath)) {
                $filesystem->put($envPath, $envVars);
                $this->info('.env copiado desde .env.example con comentarios de identificación');
            } else {
                $filesystem->append($envPath, "\n" . $envVars);
                $this->info('Variables de cms-core agregadas al final de .env existente.');
            }
        } else {
            $this->error('No se encontró .env.example en el paquete.');
        }
    }
}
