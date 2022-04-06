<?php

namespace phpcommon\http;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use phpcommon\Handler\Exceptions\MICROSERVICE_EXCEPTION;

class RequestProvider
{
    public static function post(string $url, array $data = [], $ignoreExceptions = false)
    {
        $response = Http::post($url, $data, $ignoreExceptions = false);
        self::validate($response, $ignoreExceptions);
        return $response;
    }

    private static function validate($response, $ignoreExceptions)
    {
        if ($response->failed()) {
            throw_unless($ignoreExceptions, new MICROSERVICE_EXCEPTION());

            // Строка ошибки
            Log::warning($response);
        }

        return $response;
    }

    public static function get(string $url, array|string|null $query = null, $ignoreExceptions = false)
    {
        $response = Http::get($url, $query, $ignoreExceptions);
        self::validate($response, $ignoreExceptions);
        return $response;
    }

    public static function put(string $url, array $data = [], $ignoreExceptions = false)
    {
        $response = Http::put($url, $data, $ignoreExceptions);
        self::validate($response, $ignoreExceptions);
        return $response;
    }

    public static function patch(string $url, array $data = [], $ignoreExceptions = false)
    {
        $response = Http::patch($url, $data, $ignoreExceptions);
        self::validate($response, $ignoreExceptions);
        return $response;
    }

    public static function delete(string $url, array $data = [], $ignoreExceptions = false)
    {
        $response = Http::delete($url, $data, $ignoreExceptions);
        self::validate($response, $ignoreExceptions);
        return $response;
    }

    public static function postFile($url, string|array $name, $data = [], string $contents = '', string|null $filename = null, array $headers = [], $ignoreExceptions = false)
    {
        $response = Http::attach($name, $contents, $filename, $headers)->post($url, $data);
        self::validate($response, $ignoreExceptions);
        return $response;
    }

    public static function __callStatic($name, $arguments)
    {
        $response = Http::send($name, $arguments['url'], $arguments['data']);
        dd($response);
    }
}
