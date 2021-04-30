<?php

namespace Tests\Unit;

use App\Libraries\UrlUtils;
use Tests\TestCase;

class UrlUtilsTest extends TestCase
{

    public function testCheckRemoteFile()
    {
        $json = UrlUtils::checkRemoteFile("https://httpbin.org/bytes/10", 5);
        $this->assertTrue(is_array($json));

        $this->assertArrayHasKey('success', $json);
        $this->assertTrue($json['success']);
        $this->assertArrayHasKey('info', $json);
        $this->assertEquals('200', $json['info']['http_code']);
    }

    public function testCheckRemoteFileNon200()
    {
        $json = UrlUtils::checkRemoteFile("https://httpbin.org/status/404");
        $this->assertTrue(is_array($json));

        $this->assertArrayHasKey('success', $json);
        $this->assertFalse($json['success']);
        $this->assertArrayHasKey('message', $json);
        $this->assertArrayHasKey('info', $json);
        $this->assertEquals('404', $json['info']['http_code']);
    }

    public function testGetHeaders()
    {
        $json = UrlUtils::getHeaders("https://httpbin.org/bytes/10", 5);
        $this->assertTrue(is_array($json));

        $this->assertArrayHasKey('success', $json);
        $this->assertTrue($json['success']);
        $this->assertArrayHasKey('info', $json);
        $this->assertEquals('200', $json['info']['http_code']);
    }

    public function testGetHeadersNon200()
    {
        $json = UrlUtils::getHeaders("https://httpbin.org/status/404", 5);
        $this->assertTrue(is_array($json));

        $this->assertArrayHasKey('success', $json);
        $this->assertFalse($json['success']);
        $this->assertArrayHasKey('message', $json);
        $this->assertArrayHasKey('info', $json);
        $this->assertEquals('404', $json['info']['http_code']);
    }

    public function testGetRemoteMD5()
    {
        $json = UrlUtils::get_remote_md5("https://httpbin.org/base64/dGVzdA==", 5);
        $this->assertTrue(is_array($json));

        $this->assertArrayHasKey('success', $json);
        $this->assertTrue($json['success']);
        $this->assertArrayHasKey('md5', $json);
        $this->assertEquals('098f6bcd4621d373cade4e832627b4f6', $json['md5']);
        $this->assertArrayHasKey('filesize', $json);
        $this->assertEquals('4', $json['filesize']);
    }

    public function testGetRemoteMD5Non200()
    {
        $json = UrlUtils::get_remote_md5("https://httpbin.org/status/404", 5);
        $this->assertTrue(is_array($json));

        $this->assertArrayHasKey('success', $json);
        $this->assertFalse($json['success']);
        $this->assertArrayHasKey('message', $json);
        $this->assertArrayHasKey('info', $json);
        $this->assertEquals('404', $json['info']['http_code']);
    }
}
