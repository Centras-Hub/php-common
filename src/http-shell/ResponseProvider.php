<?php

namespace phpcommon\http;

use Exception;
use phpcommon\Handler\Exceptions\MICROSERVICE_EXCEPTION;
use phpcommon\http\ResponseMessagesDTO as DTO;
use Symfony\Component\HttpFoundation\Cookie;

class ResponseProvider
{


    public static function render(DTO $body = null, Cookie $cookie = null, Exception $exception = null, string $type = "json", array $previousTrace = null)
    {
        if ($exception) {
            $trace = self::renderTrace($exception);
            $body->setTrace($previousTrace === null ? $trace : array_merge($trace, $previousTrace));
        }
        $data = $body->serialize();
        if ($cookie) {
            return response()->json($data, $body->getStatus())->cookie($cookie);;
        }
        return response()->json(data: $data, status: $body->getStatus());
    }

    private static function renderTrace(Exception $exception)
    {

        $app_name = env('APP_NAME', 'unknown_name');
        $limit_trace = env('LIMIT_TRACE', 5);

        //$old_trace =  json_decode($exception->getMessage(), true)['headers']['trace'];
        $exception_traces = $exception->getTrace();

        $limit_trace = $limit_trace >= count($exception_traces) ? count($exception_traces) : (int)$limit_trace;

        //render trace
        
        $trace = [];

       
        //$trace += $old_trace;

        //render exception trace
        $trace += [$app_name => []];

        for ($i = 0; $i < $limit_trace; $i++) {

            //create trace line like:  App\\Http\\Controllers\\AuthController : 42
            if (array_key_exists('line', $exception_traces[$i]) && array_key_exists('class', $exception_traces[$i])) {
                $trace_line = $exception_traces[$i]['class'] . " : " . $exception_traces[$i]['line'];

                array_push($trace[$app_name], $trace_line);
            }
        }

        return $trace;

    }
}
