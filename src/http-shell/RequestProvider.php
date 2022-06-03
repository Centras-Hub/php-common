<?php

namespace phpcommon\http;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use phpcommon\Handler\Exceptions\MICROSERVICE_EXCEPTION;

class RequestProvider
{


    private static function validate($response, $ignoreExceptions)
    {
        if ($response->failed()) {
            $previousTrace = $response->json()['data']['trace'];
            $response = ResponseProvider::render(new ResponseMessagesDTO(new Messages\MICROSERVICE_EXCEPTION_Message), exception: new MICROSERVICE_EXCEPTION(), previousTrace: $previousTrace);
            // Строка ошибки
            Log::warning($response);
        }

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
        $defaultValues = ['ignoreExceptions' => false, 'data' => []];
        foreach($defaultValues as $key => $value) {
            if(!in_array($key, $arguments)) {
                $arguments+=[$key => $value];
            }
        }
        $response = call_user_func([Http::class, $name], $arguments['url'], $arguments['data']);
        return self::validate($response, $arguments['ignoreExceptions']);
    }
}
