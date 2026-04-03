<?php

namespace Tests\Unit;

use App\Libraries\UrlUtils;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
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

    /**
     * @return array<string, array{int, string}>
     */
    public static function connectExceptionProvider(): array
    {
        return [
            'dns resolution failure' => [
                6,
                'Could not resolve hostname. Verify that SOLDER_REPO_LOCATION points to a valid domain.',
            ],
            'connection refused' => [
                7,
                'Connection refused by the remote server. Ensure it is running and accessible from the Solder host.',
            ],
            'connection timeout' => [
                28,
                'Connection timed out. Consider increasing SOLDER_MD5_CONNECT_TIMEOUT or SOLDER_MD5_FILE_TIMEOUT.',
            ],
            'unknown curl error' => [
                99,
                'Network error (cURL error 99). Check connectivity to the remote server.',
            ],
        ];
    }

    #[DataProvider('connectExceptionProvider')]
    public function test_get_remote_md5_connect_exception(int $errno, string $expectedMessage): void
    {
        $request = new Request('GET', 'https://test.invalid/test');
        $exception = new ConnectException(
            "cURL error {$errno}",
            $request,
            null,
            ['errno' => $errno],
        );

        $mockHandler = HandlerStack::create(
            new MockHandler([$exception])
        );

        $json = UrlUtils::get_remote_md5('https://test.invalid/test', $mockHandler);

        $this->assertIsArray($json);
        $this->assertArrayHasKey('success', $json);
        $this->assertFalse($json['success']);
        $this->assertArrayHasKey('message', $json);
        $this->assertEquals($expectedMessage, $json['message']);
    }
}
