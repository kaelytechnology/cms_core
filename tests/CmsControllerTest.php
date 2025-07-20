<?php

namespace Kaely\CmsCore\Tests;

use Kaely\AuthPackage\Models\Module;
use Illuminate\Support\Facades\Schema;

class CmsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear tabla modules si no existe (para tests)
        if (!Schema::hasTable('modules')) {
            Schema::create('modules', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('type')->default('CMS');
                $table->integer('order')->default(0);
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->string('route')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /** @test */
    public function it_can_get_cms_modules()
    {
        // Crear módulo de prueba
        Module::create([
            'name' => 'Test CMS Module',
            'slug' => 'test-cms-module',
            'type' => 'CMS',
            'description' => 'Test module',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/cms/modules');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                    'timestamp',
                    'type',
                ],
            ]);
    }

    /** @test */
    public function it_can_get_cms_stats()
    {
        $response = $this->getJson('/api/cms/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_modules',
                    'active_modules',
                    'inactive_modules',
                ],
                'meta',
            ]);
    }

    /** @test */
    public function it_can_get_cms_status()
    {
        $response = $this->getJson('/api/cms/status');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'cms_modules' => [
                    'active',
                    'type',
                ],
                'auth_integration' => [
                    'enabled',
                    'status',
                ],
            ]);
    }
} 