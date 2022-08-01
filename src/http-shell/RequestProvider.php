<?php

namespace phpcommon\http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use phpcommon\Handler\Exceptions\MICROSERVICE_EXCEPTION;
use Psr\Http\Message\ResponseInterface;

class RequestProvider
{

    private static function validate($response, $ignoreExceptions)
    {
        if ($response->failed() && !$ignoreExceptions) {
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
        $defaultValues = ['ignoreExceptions' => false, 'headers' => [], 'data' => []];
        foreach($defaultValues as $key => $value) {
            if(!array_key_exists($key, $arguments)) {
                $arguments[$key] = $value;
            }
        }
        //TODO
        $url = 'http://log-service/api/logs';
        $reqLog = self::setLog($url, $arguments);
        $reqUuid = $reqLog->data->uuid;

        $response = Http::withHeaders($arguments['headers'])
            ->{$name}($arguments['url'], $arguments['data']);

        $resLog = self::setLog($url, $response, $reqUuid, true);
        //TODO
        return self::validate($response, $arguments['ignoreExceptions']);
    }

    public static function setLog($url, $data, $reqUuid = false, $isResponse = false) {
        $client = new Client();
        //TODO
        if(isset($reqUuid) && $isResponse === true) {
            $url = 'http://log-service/api/logs/updateByUuid/' . $reqUuid;
            $data = [
                'json' => [
                    'status_code' => $data->getStatusCode(),
                    'response_header' => $data->headers(),
                    'response_data' => $data->body(),
                    'response_status_code' => $data->getStatusCode()
                ]
            ];
        } else {
            $url = 'http://log-service/api/logs';
            $data = [
                'json' => [
                    'url' => $data['url'],
                    'request_header' => $data['headers'],
                    'request_data' => $data['data'],
                ]
            ];
        }

        $promise = $client->postAsync($url, $data)->then(
            function (ResponseInterface $res){
                $log = json_decode($res->getBody()->getContents());
                return $log;
            },
            function (RequestException $e) {
                $log = [];
                $log = $e->getMessage();
                return $log;
            }
        );
        return $log = $promise->wait();
    }
}
