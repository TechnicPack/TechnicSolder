<?php

namespace Tests\Unit;

use App\Libraries\UrlUtils;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

final class UrlUtilsTest extends TestCase
{
    public function test_get_remote_md5(): void
    {
        $mockHandler = HandlerStack::create(
            new MockHandler([
                new Response(200, [], 'test'),
            ])
        );

        $json = UrlUtils::get_remote_md5('https://test.invalid/test', $mockHandler);
        $this->assertTrue(is_array($json));

        $this->assertArrayHasKey('success', $json);
        $this->assertTrue($json['success']);
        $this->assertArrayHasKey('md5', $json);
        $this->assertEquals('098f6bcd4621d373cade4e832627b4f6', $json['md5']);
        $this->assertArrayHasKey('filesize', $json);
        $this->assertEquals('4', $json['filesize']);
    }

    public function test_get_remote_md5_non_200(): void
    {
        $mockHandler = HandlerStack::create(
            new MockHandler([
                new Response(404),
            ])
        );

        $json = UrlUtils::get_remote_md5('https://test.invalid/404', $mockHandler);
        $this->assertTrue(is_array($json));

        $this->assertArrayHasKey('success', $json);
        $this->assertFalse($json['success']);
        $this->assertArrayHasKey('message', $json);
        $this->assertEquals('Expected status code 200, got 404', $json['message']);
    }
}
