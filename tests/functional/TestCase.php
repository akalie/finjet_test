<?php

namespace Tests\functional;

class TestCase extends \PHPUnit\Framework\TestCase
{
    const TEST_USER = 'askaslie';
    const TEST_PASS = 'test';

    protected static $token;

    public function post(string $url, array $params)
    {
        $cURLConnection = curl_init('http://webserver' . $url);
        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

        $apiResponse = curl_exec($cURLConnection);
        curl_close($cURLConnection);

        return json_decode($apiResponse);
    }

    public function get(string $url, array $params = [])
    {
        $url .=  '?' . http_build_query($params);
        $cURLConnection = curl_init('http://webserver' . $url);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        $apiResponse = curl_exec($cURLConnection);
        curl_close($cURLConnection);

        return json_decode($apiResponse);
    }

    public function auth()
    {
        if (static::$token) {
            return static::$token;
        }
        $r = $this->post('/auth', [
            'login' => static::TEST_USER,
            'pass'  => static::TEST_PASS,
            'test' => ['test' => 1]
        ]);

        return static::$token = $r->result->token;
    }
}