<?php

class URLUtilsTest extends TestCase {

    public function testCheckRemoteFile()
	{
		$json = UrlUtils::checkRemoteFile("http://speedtest.wdc01.softlayer.com/downloads/test10.zip", 5);
        $this->assertTrue(is_array($json));

        $this->assertTrue(array_key_exists('success', $json));
        $this->assertTrue($json['success']);
        $this->assertTrue(array_key_exists('info', $json));
        $this->assertEquals('200', $json['info']['http_code']);
	}

    public function testCheckRemoteFileNon200()
	{
		$json = UrlUtils::checkRemoteFile("http://speedtest.wdc01.softlayer.com/downloads/testbob.zip", 5);
        $this->assertTrue(is_array($json));

        $this->assertTrue(array_key_exists('success', $json));
        $this->assertFalse($json['success']);
        $this->assertTrue(array_key_exists('message', $json));
        $this->assertTrue(array_key_exists('info', $json));
        $this->assertEquals('404', $json['info']['http_code']);
	}

    public function testGetHeaders()
	{
		$json = UrlUtils::getHeaders("http://speedtest.wdc01.softlayer.com/downloads/test10.zip", 5);
        $this->assertTrue(is_array($json));

        $this->assertTrue(array_key_exists('success', $json));
        $this->assertTrue($json['success']);
        $this->assertTrue(array_key_exists('info', $json));
        $this->assertEquals('200', $json['info']['http_code']);
	}

    public function testGetHeadersNon200()
	{
		$json = UrlUtils::getHeaders("http://speedtest.wdc01.softlayer.com/downloads/testbob.zip", 5);
        $this->assertTrue(is_array($json));

        $this->assertTrue(array_key_exists('success', $json));
        $this->assertFalse($json['success']);
        $this->assertTrue(array_key_exists('message', $json));
        $this->assertTrue(array_key_exists('info', $json));
        $this->assertEquals('404', $json['info']['http_code']);
	}

    public function testGetRemoteMD5()
	{
		$json = UrlUtils::get_remote_md5("http://speedtest.wdc01.softlayer.com/downloads/test10.zip", 5);
        $this->assertTrue(is_array($json));

        $this->assertTrue(array_key_exists('success', $json));
        $this->assertTrue($json['success']);
        $this->assertTrue(array_key_exists('md5', $json));
        $this->assertEquals('96e83be5f5522c835005b1b1dcec2427', $json['md5']);
        $this->assertTrue(array_key_exists('filesize', $json));
        $this->assertEquals('11536384', $json['filesize']);
	}

    public function testGetRemoteMD5Non200()
	{
		$json = UrlUtils::get_remote_md5("http://speedtest.wdc01.softlayer.com/downloads/testbob.zip", 5);
        $this->assertTrue(is_array($json));

        $this->assertTrue(array_key_exists('success', $json));
        $this->assertFalse($json['success']);
        $this->assertTrue(array_key_exists('message', $json));
        $this->assertTrue(array_key_exists('info', $json));
        $this->assertEquals('404', $json['info']['http_code']);
	}
}
