<?php


namespace phpcommon\Utils;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ServiceRegistry
{
    public static function getAddress(string $service)
    {
        try {
            $response = Http::get(env('SERVICE_REGISTRY_ADDRESS') . '/service/' . $service)->json(['data', 'address']);
            if ($response != null) {
                return $response;
            }
            throw new ModelNotFoundException();
        }
        catch (ModelNotFoundException $ex) {
            throw $ex;
        } catch (\Exception $ex) {
            return collect(Cache::get('services')['services'])->firstWhere('name', $service)['address'];
        }
    }
}
