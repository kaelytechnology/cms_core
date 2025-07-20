<?php

use Illuminate\Support\Facades\Route;
use Kaely\CmsCore\Controllers\CmsController;

/*
|--------------------------------------------------------------------------
| CMS Core API Routes
|--------------------------------------------------------------------------
|
| Aquí se definen las rutas API del paquete CMS Core.
| Todas las rutas están prefijadas con 'api/cms' por defecto.
|
*/

$prefix = config('cms-core.routes.prefix', 'cms');
$middleware = config('cms-core.routes.middleware', ['api']);
$authMiddleware = config('cms-core.routes.auth_middleware', ['auth:sanctum']);

Route::prefix($prefix)
    ->middleware($middleware)
    ->group(function () use ($authMiddleware) {
        
        // Rutas públicas (sin autenticación)
        Route::get('/modules', [CmsController::class, 'modules']);
        Route::get('/modules/{slug}', [CmsController::class, 'module']);
        Route::get('/stats', [CmsController::class, 'stats']);
        Route::get('/status', [CmsController::class, 'status']);
    }); 