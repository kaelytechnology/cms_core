<?php

namespace Kaelytechnology\CmsCore\Console;

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

        if (!$filesystem->exists($envPath)) {
            if ($filesystem->exists($examplePath)) {
                $envContent = $filesystem->get($examplePath);
                $envContent = "# === Variables de entorno para cms-core (inicio) ===\n"
                    . $envContent . "\n# === Variables de entorno para cms-core (fin) ===\n";
                $filesystem->put($envPath, $envContent);
                $this->info('.env copiado desde .env.example con comentarios de identificación');
            } else {
                $this->error('No se encontró .env.example en el paquete.');
            }
        } else {
            $this->info('El archivo .env ya existe.');
        }
    }
}
