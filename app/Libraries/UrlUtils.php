<?php

namespace App\Libraries;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class UrlUtils
{
    private const USER_AGENT = 'Mozilla/5.0 TechnicSolder/0.7 (+https://github.com/TechnicPack/TechnicSolder)';

    private const MAX_REDIRECTS = 5;

    private const DEFAULT_CONNECT_TIMEOUT = 5;

    private const DEFAULT_TOTAL_TIMEOUT = 15;

    /**
     * @var Client Guzzle client
     */
    private static Client $client;

    public static function getGuzzleClient(): Client
    {
        $configConnectTimeout = config('solder.md5_connect_timeout');
        $configTotalTimeout = config('solder.md5_file_timeout');

        return self::$client ??= new Client([
            // Disable HTTP errors (4xx, 5xx) raising exceptions
            'http_errors' => false,
            // Set maximum redirects
            'allow_redirects' => [
                'max' => self::MAX_REDIRECTS,
            ],
            // Set connection timeout
            'connect_timeout' => is_int($configConnectTimeout) ? $configConnectTimeout : self::DEFAULT_CONNECT_TIMEOUT,
            // Set total timeout
            'timeout' => is_int($configTotalTimeout) ? $configTotalTimeout : self::DEFAULT_TOTAL_TIMEOUT,
        ]);
    }

    /**
     * Uses Guzzle to get URL contents and returns hash
     *
     * @param  string  $url  Url Location
     * @return array
     */
    public static function get_remote_md5(string $url): array
    {
        // We need to return:
        // - success => true
        // - md5 => md5 hash string
        // - filesize => filesize (in bytes)
        // If it fails, we return
        // - success => false
        // - message => exception message (string) $e->getMessage()
        // And we log the error to the laravel error log

        $client = self::getGuzzleClient();

        try {
            $response = $client->get($url, [
                'headers' => [
                    'User-Agent' => self::USER_AGENT,
                ],
                'stream' => true,
            ]);

            if ($response->getStatusCode() !== 200) {
                return [
                    'success' => false,
                    'message' => 'Expected status code 200, got '.$response->getStatusCode(),
                ];
            }

            $body = $response->getBody();

            $ctx = hash_init('md5');
            $filesize = 0;

            while (! $body->eof()) {
                // Read in 64 KB chunks
                $buffer = $body->read(64 * 1024);
                $filesize += strlen($buffer);
                hash_update($ctx, $buffer);
            }

            $hash = hash_final($ctx);

            return [
                'success' => true,
                'md5' => $hash,
                'filesize' => $filesize,
            ];
        } catch (GuzzleException $e) {
            Log::error('Error hashing remote md5: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
