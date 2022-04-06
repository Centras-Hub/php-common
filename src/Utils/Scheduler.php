<?php

namespace phpcommon\Utils;

use Illuminate\Support\Facades\Http;

class Scheduler
{
    public static function heartbeat()
    {
        Http::put(env('SERVICE_REGISTRY_ADDRESS') . '/service/heartbeat', ['name' => env('APP_NAME')]);
    }
}
