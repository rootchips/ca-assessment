<?php

namespace Tests\Feature;

use Tests\TestCase;

class ArchitectureTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function root_endpoint_returns_backend_api_metadata(): void
    {
        $this->getJson('/')
            ->assertOk()
            ->assertJsonPath('type', 'backend-api')
            ->assertJsonStructure([
                'name',
                'type',
                'message',
                'api_base',
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function preflight_request_allows_frontend_origin_for_api_routes(): void
    {
        $response = $this
            ->withHeaders([
                'Origin' => 'http://localhost:5173',
                'Access-Control-Request-Method' => 'GET',
            ])
            ->options('/api/products');

        $response->assertNoContent();
        $response->assertHeader('Access-Control-Allow-Origin', 'http://localhost:5173');
    }
}
