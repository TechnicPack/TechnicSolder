<?php

namespace Tests\Feature;

use Tests\TestCase;

final class CorsMiddlewareTest extends TestCase
{
    public function test_wildcard_cors_sets_star_origin(): void
    {
        config(['solder.cors_allowed_origins' => '*']);

        $response = $this->get('/api/', ['Origin' => 'https://evil.com']);

        $response->assertHeader('Access-Control-Allow-Origin', '*');
        $this->assertFalse($response->headers->has('Vary'));
    }

    public function test_specific_origin_is_reflected_when_allowed(): void
    {
        config(['solder.cors_allowed_origins' => 'https://example.com']);

        $response = $this->get('/api/', ['Origin' => 'https://example.com']);

        $response->assertHeader('Access-Control-Allow-Origin', 'https://example.com');
        $response->assertHeader('Vary', 'Origin');
    }

    public function test_disallowed_origin_gets_no_cors_header(): void
    {
        config(['solder.cors_allowed_origins' => 'https://example.com']);

        $response = $this->get('/api/', ['Origin' => 'https://evil.com']);

        $this->assertNotEquals('https://evil.com', $response->headers->get('Access-Control-Allow-Origin'));
        $response->assertHeader('Vary', 'Origin');
    }

    public function test_comma_separated_origins_are_supported(): void
    {
        config(['solder.cors_allowed_origins' => 'https://a.com, https://b.com']);

        $responseA = $this->get('/api/', ['Origin' => 'https://a.com']);
        $responseA->assertHeader('Access-Control-Allow-Origin', 'https://a.com');

        $responseB = $this->get('/api/', ['Origin' => 'https://b.com']);
        $responseB->assertHeader('Access-Control-Allow-Origin', 'https://b.com');

        $responseC = $this->get('/api/', ['Origin' => 'https://c.com']);
        $this->assertNotEquals('https://c.com', $responseC->headers->get('Access-Control-Allow-Origin'));
    }

    public function test_no_origin_header_skips_cors_when_not_wildcard(): void
    {
        config(['solder.cors_allowed_origins' => 'https://example.com']);

        $response = $this->get('/api/');

        $this->assertNull($response->headers->get('Access-Control-Allow-Origin'));
        $response->assertHeader('Vary', 'Origin');
    }
}
