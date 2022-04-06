<?php


namespace phpcommon\Utils;


use Illuminate\Support\Facades\Http;

class ServiceLog
{
    public static function warn(string $service, string $message, string $notifications)
    {
        if ($notifications == null) {
            $notifications = ServiceRegistry::getAddress(MicroServices::NOTIFICATIONS_MICROSERVICE);
        }
        Http::post($notifications . '/system/warn', ['service' => $service, 'message' => $message]);
    }

    public static function error(string $service, string $message, string $notifications)
    {
        if ($notifications == null) {
            $notifications = ServiceRegistry::getAddress(MicroServices::NOTIFICATIONS_MICROSERVICE);
        }
        Http::post($notifications . '/system/error', ['service' => $service, 'message' => $message]);
    }

    public static function info(string $service, string $message, string $notifications)
    {
        if ($notifications == null) {
            $notifications = ServiceRegistry::getAddress(MicroServices::NOTIFICATIONS_MICROSERVICE);
        }
        Http::post($notifications . '/system/info', ['service' => $service, 'message' => $message]);
    }
}
