<?php


namespace phpcommon\Handler;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Validation\UnauthorizedException;
use InvalidArgumentException;
use phpcommon\Handler\Exceptions\BAD_QUERY_STRING_EXCEPTION;
use phpcommon\Handler\Exceptions\ENTITY_EXISTS_EXCEPTION;
use phpcommon\Handler\Exceptions\INVALID_REFRESH_TOKEN_EXCEPTION;
use phpcommon\Handler\Exceptions\INVALID_TOKEN_EXCEPTION;
use phpcommon\Handler\Exceptions\LOGIN_ERROR_EXCEPTION;
use phpcommon\Handler\Exceptions\MICROSERVICE_EXCEPTION;
use phpcommon\Handler\Exceptions\USER_VERIFICATION_EXCEPTION;
use phpcommon\http\Messages\ACCESS_DENIED_Message;
use phpcommon\http\Messages\BAD_QUERY_STRING_Message;
use phpcommon\http\Messages\BAD_REQUEST_Message;
use phpcommon\http\Messages\ENTITY_EXIST_Message;
use phpcommon\http\Messages\ENTITY_NOT_FOUND_Message;
use phpcommon\http\Messages\EXTENSION_EXCEPTION_Message;
use phpcommon\http\Messages\INVALID_TOKEN_Message;
use phpcommon\http\Messages\LOGIN_ERROR_Message;
use phpcommon\http\Messages\MICROSERVICE_EXCEPTION_Message;
use phpcommon\http\Messages\UNAUTHORIZED_ACCESS_Message;
use phpcommon\http\Messages\USER_NOT_VERIFIED_Message;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\File\Exception\ExtensionFileException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionMessageMap{

    public static $map = [
        ENTITY_NOT_FOUND_Message::class => [
            NotFoundHttpException::class,
            ModelNotFoundException::class
        ],
        BAD_REQUEST_Message::class=> [
            BadRequestHttpException::class,
             BadRequestException::class,
             InvalidArgumentException::class
            ],
        UNAUTHORIZED_ACCESS_Message::class => [
            UnauthorizedException::class
        ],
        EXTENSION_EXCEPTION_Message::class => [
            ExtensionFileException::class
        ],
        ACCESS_DENIED_Message::class => [
            AccessDeniedException::class,
             AccessDeniedHttpException::class
            ],
        INVALID_TOKEN_Message::class => [
            INVALID_TOKEN_EXCEPTION::class,
             INVALID_REFRESH_TOKEN_EXCEPTION::class
            ],
        USER_NOT_VERIFIED_Message::class => [
            USER_VERIFICATION_EXCEPTION::class
        ],
        LOGIN_ERROR_Message::class => [
            LOGIN_ERROR_EXCEPTION::class
        ],
        ENTITY_EXIST_Message::class => [
            ENTITY_EXISTS_EXCEPTION::class
        ],
        MICROSERVICE_EXCEPTION_Message::class => [
            MICROSERVICE_EXCEPTION::class
        ],
        BAD_QUERY_STRING_Message::class => [
            BAD_QUERY_STRING_EXCEPTION::class, RelationNotFoundException::class
            ]




    ];
}
