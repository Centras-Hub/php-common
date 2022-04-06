<?php

namespace phpcommon\Utils;

use phpcommon\http\RequestProvider;

class File
{
    /**
     * Returns downloaded base64 file
     */
    public static function download_base64(string $uuid, $ignoreExceptions = false): string
    {
        return 'data:image/jpeg;base64,' . base64_encode(RequestProvider::get(ServiceRegistry::getAddress(MicroServices::FILES_MICROSERVICE) . File::download($uuid), ignoreExceptions: $ignoreExceptions)->body());
    }

    /**
     * Returns download url
     */
    public static function download(string $uuid, $ignoreExceptions = false): string
    {
        return '/file/' . $uuid . '/download';
    }

    /**
     * Deletes file and returns HTTP response
     */
    public static function delete(string $uuid, $ignoreExceptions = false)
    {
        return RequestProvider::delete(ServiceRegistry::getAddress(MicroServices::FILES_MICROSERVICE) . '/file/' . $uuid, ignoreExceptions: $ignoreExceptions);
    }

    /**
     * Uploads file and returns file UUID
     */
    public static function upload($file, string $fileName, $delete_at = null, $ignoreExceptions = false): string
    {
        return RequestProvider::postFile(url: ServiceRegistry::getAddress(MicroServices::FILES_MICROSERVICE) . '/file', name: 'file', data: ['delete_at' => $delete_at], contents: $file, filename: $fileName, headers: [], ignoreExceptions: $ignoreExceptions)->json(['data', 'uuid']);
    }
}
