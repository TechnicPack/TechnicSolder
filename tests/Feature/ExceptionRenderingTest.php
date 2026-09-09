<?php

namespace Tests\Feature;

use Tests\TestCase;

final class ExceptionRenderingTest extends TestCase
{
    public function test_unmatched_api_route_returns_json_even_when_html_is_requested(): void
    {
        $this->get('/api/nonexistent-route', ['Accept' => 'text/html'])
            ->assertNotFound()
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_unsupported_api_method_returns_json_before_route_middleware_runs(): void
    {
        $this->delete('/api/', [], ['Accept' => 'text/html'])
            ->assertStatus(405)
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_unmatched_web_route_preserves_html_error_response(): void
    {
        $this->get('/nonexistent-route', ['Accept' => 'text/html'])
            ->assertNotFound()
            ->assertHeader('Content-Type', 'text/html; charset=utf-8');
    }
}
