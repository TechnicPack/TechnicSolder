<?php

namespace Tests\Unit;

use App\Libraries\UrlUtils;
use Tests\TestCase;

class UrlUtilsTest extends TestCase
{
    public function test_get_remote_md5(): void
    {
        $json = UrlUtils::get_remote_md5('https://httpbin.org/base64/dGVzdA==', 5);
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
        $json = UrlUtils::get_remote_md5('https://httpbin.org/status/404', 5);
        $this->assertTrue(is_array($json));

        $this->assertArrayHasKey('success', $json);
        $this->assertFalse($json['success']);
        $this->assertArrayHasKey('message', $json);
        $this->assertEquals('Expected status code 200, got 404', $json['message']);
    }
}
