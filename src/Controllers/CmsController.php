<?php

namespace Kaely\CmsCore\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Kaely\AuthPackage\Models\Module;

class CmsController extends Controller
{
    /**
     * Obtener módulos de tipo CMS
     */
    public function modules(Request $request): JsonResponse
    {
        try {
            $perPage = min(
                (int) $request->query('per_page', config('cms-core.api.default_pagination', 15)),
                config('cms-core.api.max_pagination', 100)
            );

            $query = Module::where('type', config('cms-core.modules.type', 'CMS'))
                ->where('is_active', true)
                ->orderBy('order')
                ->orderBy('name');

            // Aplicar filtros adicionales
            if ($request->has('search')) {
                $search = $request->query('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Aplicar paginación
            $modules = $query->paginate($perPage);

            $response = [
                'data' => $modules->items(),
                'meta' => [
                    'current_page' => $modules->currentPage(),
                    'last_page' => $modules->lastPage(),
                    'per_page' => $modules->perPage(),
                    'total' => $modules->total(),
                    'from' => $modules->firstItem(),
                    'to' => $modules->lastItem(),
                ],
            ];

            // Agregar metadatos si está habilitado
            if (config('cms-core.api.include_metadata', true)) {
                $response['meta']['timestamp'] = now()->toISOString();
                $response['meta']['type'] = config('cms-core.modules.type', 'CMS');
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener módulos CMS',
                'message' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Obtener información de un módulo CMS específico
     */
    public function module(string $slug): JsonResponse
    {
        try {
            $module = Module::where('type', config('cms-core.modules.type', 'CMS'))
                ->where('slug', $slug)
                ->where('is_active', true)
                ->first();

            if (!$module) {
                return response()->json([
                    'error' => 'Módulo CMS no encontrado',
                ], 404);
            }

            $response = [
                'data' => $module,
            ];

            // Agregar metadatos si está habilitado
            if (config('cms-core.api.include_metadata', true)) {
                $response['meta'] = [
                    'timestamp' => now()->toISOString(),
                    'type' => config('cms-core.modules.type', 'CMS'),
                ];
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener información del módulo',
                'message' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de módulos CMS
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_modules' => Module::where('type', config('cms-core.modules.type', 'CMS'))->count(),
                'active_modules' => Module::where('type', config('cms-core.modules.type', 'CMS'))
                    ->where('is_active', true)
                    ->count(),
                'inactive_modules' => Module::where('type', config('cms-core.modules.type', 'CMS'))
                    ->where('is_active', false)
                    ->count(),
            ];

            $response = [
                'data' => $stats,
            ];

            // Agregar metadatos si está habilitado
            if (config('cms-core.api.include_metadata', true)) {
                $response['meta'] = [
                    'timestamp' => now()->toISOString(),
                    'type' => config('cms-core.modules.type', 'CMS'),
                ];
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener estadísticas',
                'message' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Obtener el estado del sistema CMS
     */
    public function status(): JsonResponse
    {
        try {
            $cmsModules = Module::where('type', config('cms-core.modules.type', 'CMS'))
                ->where('is_active', true)
                ->count();

            $status = [
                'status' => 'operational',
                'timestamp' => now()->toISOString(),
                'cms_modules' => [
                    'active' => $cmsModules,
                    'type' => config('cms-core.modules.type', 'CMS'),
                ],
                'auth_integration' => [
                    'enabled' => config('cms-core.auth_integration.enabled', true),
                    'status' => $this->checkAuthIntegration(),
                ],
            ];

            // Agregar metadatos si está habilitado
            if (config('cms-core.api.include_metadata', true)) {
                $status['metadata'] = [
                    'environment' => app()->environment(),
                    'debug' => config('app.debug', false),
                ];
            }

            return response()->json($status);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener el estado del sistema',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Verificar integración con auth-package
     */
    protected function checkAuthIntegration(): string
    {
        if (!config('cms-core.auth_integration.enabled', true)) {
            return 'disabled';
        }

        if (!class_exists('\Kaely\AuthPackage\Models\Module')) {
            return 'auth_package_not_found';
        }

        return 'active';
    }
} 