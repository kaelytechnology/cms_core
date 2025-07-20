<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CMS Core Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the CMS Core package.
    | You can customize these settings according to your needs.
    |
    */

    // Configuración de rutas
    'routes' => [
        'prefix' => env('CMS_ROUTES_PREFIX', 'cms'),
        'api_prefix' => env('CMS_ROUTES_API_PREFIX', 'api'),
        'middleware' => explode(',', env('CMS_ROUTES_MIDDLEWARE', 'api')),
        'auth_middleware' => explode(',', env('CMS_ROUTES_AUTH_MIDDLEWARE', 'auth:sanctum')),
    ],

    // Configuración de módulos
    'modules' => [
        'type' => env('CMS_MODULES_TYPE', 'CMS'),
        'default_status' => env('CMS_MODULES_DEFAULT_STATUS', 'active'),
    ],

    // Configuración de API
    'api' => [
        'include_metadata' => env('CMS_API_INCLUDE_METADATA', true),
        'default_pagination' => env('CMS_API_DEFAULT_PAGINATION', 15),
        'max_pagination' => env('CMS_API_MAX_PAGINATION', 100),
    ],

    // Configuración de integración con auth-package
    'auth_integration' => [
        'enabled' => env('CMS_AUTH_INTEGRATION_ENABLED', true),
        'auto_register_modules' => env('CMS_AUTO_REGISTER_MODULES', true),
    ],
]; 