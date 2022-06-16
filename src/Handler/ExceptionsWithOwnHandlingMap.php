<?php

namespace phpcommon\Handler;

use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Validation\ValidationException;
use phpcommon\http\Messages\BAD_QUERY_STRING_Message;
use phpcommon\http\ResponseMessagesDTO;
use phpcommon\http\ResponseProvider;

class ExceptionsWithOwnHandlingMap {


    public static function getMap() {
        return [
            ValidationException::class => function($e) {
                return response()->json($e->validator->errors(), 422);
            },
            RelationNotFoundException::class => function($e) {
                return ResponseProvider::render(new ResponseMessagesDTO(new BAD_QUERY_STRING_Message($e->getMessage())), exception: $e);
            }
        ];
    }
}