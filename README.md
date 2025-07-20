# CMS Core

Núcleo del CMS modular basado en API para Laravel 12 que reutiliza la tabla `modules` del `auth-package`.

## Características

- 🚀 **Laravel 12** - Compatible con la última versión de Laravel
- 🔌 **Integración con auth-package** - Reutiliza la tabla `modules` existente
- 📊 **API First** - Diseñado exclusivamente para APIs
- 🏷️ **Clasificación por tipos** - Distingue módulos CMS, POS, CLINIC, etc.
- 🛠️ **Comandos Artisan** - Herramientas de gestión desde consola
- 🔐 **Menú dinámico** - Integración automática con el sistema de menús

## Instalación

### 1. Instalar el paquete

```bash
composer require kaelytechnology/cms-core
```

### 2. Publicar configuraciones y migraciones

```bash
php artisan vendor:publish --tag=cms-core-config
php artisan vendor:publish --tag=cms-core-migrations
```

### 3. Ejecutar migraciones

```bash
php artisan migrate
```

### 4. Instalar CMS Core

```bash
php artisan cms:install
```

## Configuración

### Variables de entorno

Agrega estas variables a tu archivo `.env`:

```env
# Rutas del CMS
CMS_ROUTES_PREFIX=cms
CMS_ROUTES_API_PREFIX=api
CMS_ROUTES_MIDDLEWARE=api
CMS_ROUTES_AUTH_MIDDLEWARE=auth:sanctum

# Configuración de módulos
CMS_MODULES_TYPE=CMS
CMS_MODULES_DEFAULT_STATUS=active

# API
CMS_API_INCLUDE_METADATA=true
CMS_API_DEFAULT_PAGINATION=15
CMS_API_MAX_PAGINATION=100

# Integración con auth-package
CMS_AUTH_INTEGRATION_ENABLED=true
CMS_AUTO_REGISTER_MODULES=true
```

## Uso

### Rutas API disponibles

#### Públicas (sin autenticación)

- `GET /api/cms/modules` - Lista de módulos CMS
- `GET /api/cms/modules/{slug}` - Información de un módulo CMS específico
- `GET /api/cms/stats` - Estadísticas de módulos CMS
- `GET /api/cms/status` - Estado del sistema CMS

### Comandos Artisan

#### Instalar CMS Core
```bash
php artisan cms:install
```

#### Registrar un módulo CMS
```bash
php artisan cms:register-module "Mi Módulo" \
    --slug="mi-modulo" \
    --description="Descripción de mi módulo" \
    --icon="fas fa-file" \
    --route="/api/cms/mi-modulo" \
    --order=2
```

### Registro de módulos programáticamente

```php
use Kaely\AuthPackage\Models\Module;

// Registrar un módulo CMS
Module::updateOrCreate(
    ['slug' => 'mi-modulo'],
    [
        'name' => 'Mi Módulo',
        'type' => 'CMS',
        'description' => 'Descripción de mi módulo',
        'icon' => 'fas fa-file',
        'route' => '/api/cms/mi-modulo',
        'order' => 2,
        'is_active' => true,
    ]
);
```

## Estructura de base de datos

### Tabla `modules` (auth-package)

El paquete agrega el campo `type` a la tabla `modules` existente:

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | ID único |
| `name` | string | Nombre del módulo |
| `slug` | string | Slug único del módulo |
| `type` | string | **NUEVO** - Tipo de módulo (CMS, POS, CLINIC, etc.) |
| `order` | integer | Orden del módulo |
| `description` | text | Descripción del módulo |
| `icon` | string | Icono del módulo (FontAwesome) |
| `route` | string | Ruta del módulo |
| `is_active` | boolean | Si el módulo está activo |
| `timestamps` | timestamps | Campos de tiempo |
| `softDeletes` | softDeletes | Soft deletes |
| `user_add` | bigint | Usuario que creó el módulo |
| `user_edit` | bigint | Usuario que editó el módulo |
| `user_deleted` | bigint | Usuario que eliminó el módulo |

## Integración con auth-package

El paquete se integra automáticamente con `auth-package` para:

- **Reutilizar la tabla `modules`** existente
- **Agregar clasificación por tipos** mediante el campo `type`
- **Registrar en el menú dinámico** automáticamente
- **Mantener compatibilidad** con el sistema de permisos existente

### Tipos de módulos soportados

- `CMS` - Módulos del sistema CMS
- `POS` - Módulos de punto de venta
- `CLINIC` - Módulos de clínica
- `ADMIN` - Módulos administrativos
- `CUSTOM` - Módulos personalizados

## Ejemplos de uso

### Obtener módulos CMS

```bash
curl -X GET "http://localhost:8000/api/cms/modules"
```

Respuesta:
```json
{
    "data": [
        {
            "id": 1,
            "name": "CMS Core",
            "slug": "cms-core",
            "type": "CMS",
            "description": "Núcleo del sistema CMS modular",
            "icon": "fas fa-cogs",
            "route": "/api/cms/modules",
            "order": 1,
            "is_active": true
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 15,
        "total": 1,
        "timestamp": "2024-01-01T00:00:00.000000Z",
        "type": "CMS"
    }
}
```

### Registrar módulo desde código

```php
// En el ServiceProvider de tu módulo
public function boot()
{
    // Registrar módulo en auth-package
    Module::updateOrCreate(
        ['slug' => 'mi-modulo-cms'],
        [
            'name' => 'Mi Módulo CMS',
            'type' => 'CMS',
            'description' => 'Descripción de mi módulo CMS',
            'icon' => 'fas fa-file',
            'route' => '/api/cms/mi-modulo',
            'order' => 2,
            'is_active' => true,
        ]
    );
}
```

## Desarrollo

### Estructura del paquete

```
cms-core/
├── composer.json                    # Configuración del paquete
├── README.md                       # Documentación
├── src/
│   ├── CmsCoreServiceProvider.php  # Service Provider principal
│   ├── Controllers/
│   │   └── CmsController.php       # Controlador principal
│   └── Console/
│       ├── InstallCmsCore.php      # Comando de instalación
│       └── RegisterCmsModule.php   # Comando de registro
├── config/
│   └── cms-core.php                # Archivo de configuración
├── database/
│   └── migrations/
│       └── add_type_to_modules_table.php
└── routes/
    └── api.php                     # Rutas API
```

## Migración desde auth-package

Si ya tienes módulos en `auth-package`, el campo `type` se agregará automáticamente con el valor por defecto `CMS`. Puedes actualizar los tipos manualmente:

```sql
-- Actualizar módulos existentes
UPDATE modules SET type = 'CMS' WHERE type IS NULL;

-- Asignar tipos específicos
UPDATE modules SET type = 'POS' WHERE slug IN ('pos-sales', 'pos-inventory');
UPDATE modules SET type = 'CLINIC' WHERE slug IN ('clinic-patients', 'clinic-appointments');
```

## Licencia

MIT License - ver archivo [LICENSE](LICENSE) para más detalles.

## Soporte

Para soporte técnico o preguntas:

- 📧 Email: dev@kaely.com
- 📖 Documentación: [Enlace a documentación]
- 🐛 Issues: [Enlace al repositorio]

## Changelog

### v1.0.0
- Lanzamiento inicial
- Integración con auth-package
- Campo `type` para clasificación de módulos
- API REST para módulos CMS
- Comandos Artisan
- Registro automático en menú dinámico 