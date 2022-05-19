<?php

namespace App\Libraries;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Config;
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
                'max' => 5
            ],
            // Set connection timeout
            'connect_timeout' => is_int($configConnectTimeout) ? $configConnectTimeout : self::DEFAULT_CONNECT_TIMEOUT,
            // Set total timeout
            'timeout' => is_int($configTotalTimeout) ? $configTotalTimeout : self::DEFAULT_TOTAL_TIMEOUT,
        ]);
    }

    /**
     * Initializes a cURL session with common options
     * @param  string  $url
     * @return CurlHandle|false
     */
    private static function curl_init($url)
    {
        $ch = curl_init($url);

        if (Config::has('solder.md5_connect_timeout')) {
            $timeout = config('solder.md5_connect_timeout');
            if (is_int($timeout)) {
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            }
        } else {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::DEFAULT_CONNECT_TIMEOUT);
        }

        if (Config::has('solder.md5_file_timeout')) {
            $timeout = config('solder.md5_file_timeout');
            if (is_int($timeout)) {
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            }
        } else {
            curl_setopt($ch, CURLOPT_TIMEOUT, self::DEFAULT_TOTAL_TIMEOUT);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, self::MAX_REDIRECTS);
        curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        return $ch;
    }

    /**
     * Gets URL contents and returns them
     * @param  string  $url
     * @return array
     */
    public static function get_url_contents($url)
    {
        $ch = self::curl_init($url);

        $data = curl_exec($ch);
        $info = curl_getinfo($ch);

        if (! curl_errno($ch)) {
            //check HTTP return code
            curl_close($ch);
            if ($info['http_code'] == 200) {
                return [
                    'success' => true,
                    'data' => $data,
                    'info' => $info,
                ];
            } else {
                Log::error('Curl error for '.$url.': URL returned status code - '.$info['http_code']);

                return [
                    'success' => false,
                    'message' => 'URL returned status code - '.$info['http_code'],
                    'info' => $info,
                ];
            }
        }

        $errors = curl_error($ch);
        //log the string return of the errors
        Log::error('Curl error for '.$url.': '.$errors);
        curl_close($ch);

        return [
            'success' => false,
            'message' => $errors,
            'info' => $info,
        ];
    }

    /**
     * Uses Guzzle to get URL contents and returns hash
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
                    'message' => 'Expected status code 200, got ' . $response->getStatusCode(),
                ];
            }

            $body = $response->getBody();

            $ctx = hash_init('md5');
            $filesize = 0;

            while (!$body->eof()) {
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
            Log::error('Error hashing remote md5: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public static function checkRemoteFile($url)
    {
        $ch = self::curl_init($url);

        curl_setopt($ch, CURLOPT_NOBODY, true);

        curl_exec($ch);

        $info = curl_getinfo($ch);

        //check if there are any errors
        if (! curl_errno($ch)) {
            //check HTTP return code
            curl_close($ch);
            if ($info['http_code'] == 200 || $info['http_code'] == 405) {
                return ['success' => true, 'info' => $info];
            } else {
                return [
                    'success' => false,
                    'message' => 'URL returned status code - '.$info['http_code'],
                    'info' => $info,
                ];
            }
        }

        //log the string return of the errors
        $errors = curl_error($ch);
        Log::error('Curl error for '.$url.': '.$errors);
        curl_close($ch);

        return ['success' => false, 'message' => $errors, 'info' => $info];
    }

    public static function getHeaders($url)
    {
        $ch = self::curl_init($url);

        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $data = curl_exec($ch);
        $info = curl_getinfo($ch);

        if (! curl_errno($ch)) {
            //check HTTP return code
            curl_close($ch);
            if ($info['http_code'] == 200 || $info['http_code'] == 405) {
                return ['success' => true, 'headers' => $data, 'info' => $info];
            } else {
                return [
                    'success' => false, 'message' => 'Remote server did not return 200', 'info' => $info,
                ];
            }
        }

        //log the string return of the errors
        $errors = curl_error($ch);
        Log::error('Curl error for '.$url.': '.$errors);
        curl_close($ch);

        return ['success' => false, 'message' => $errors, 'info' => $info];
    }
}
