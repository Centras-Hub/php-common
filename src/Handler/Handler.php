<?php

namespace phpcommon\Handler;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use phpcommon\Handler\Exceptions as CreatorsExceptions;
use phpcommon\http\Messages;
use phpcommon\http\ResponseMessagesDTO;
use phpcommon\http\ResponseProvider;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\File\Exception\ExtensionFileException;
use Symfony\Component\HttpKernel\Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $e
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return Response|JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response|JsonResponse
    {
        switch ($e) {
            case ($e instanceof ModelNotFoundException):
            case ($e instanceof Exception\NotFoundHttpException):
                return ResponseProvider::render(new ResponseMessagesDTO(new Messages\ENTITY_NOT_FOUND_Message), exception: $e);
            case ($e instanceof BadRequestHttpException):
            case ($e instanceof BadRequestException):
            case ($e instanceof InvalidArgumentException):
                return ResponseProvider::render(new ResponseMessagesDTO(new Messages\BAD_REQUEST_Message), exception: $e);
            case ($e instanceof UnauthorizedException):
                return ResponseProvider::render(new ResponseMessagesDTO(new Messages\UNAUTHORIZED_ACCESS_Message), exception: $e);
            case ($e instanceof ExtensionFileException):
                return ResponseProvider::render(new ResponseMessagesDTO(new Messages\EXTENSION_EXCEPTION_Message), exception: $e);
            case ($e instanceof ValidationException):
                return response()->json($e->validator->errors(), 422);
            case ($e instanceof AccessDeniedException):
            case ($e instanceof Exception\AccessDeniedHttpException):
                return ResponseProvider::render(new ResponseMessagesDTO(new Messages\ACCESS_DENIED_Message), exception: $e);

            // Custom exceptions
            case ($e instanceof CreatorsExceptions\INVALID_TOKEN_EXCEPTION):
            case ($e instanceof CreatorsExceptions\INVALID_REFRESH_TOKEN_EXCEPTION):
                return ResponseProvider::render(new ResponseMessagesDTO(new Messages\INVALID_TOKEN_Message), exception: $e);
            case ($e instanceof CreatorsExceptions\USER_VERIFICATION_EXCEPTION):
                return ResponseProvider::render(new ResponseMessagesDTO(new Messages\USER_NOT_VERIFIED_Message), exception: $e);
            case ($e instanceof CreatorsExceptions\LOGIN_ERROR_EXCEPTION):
                return ResponseProvider::render(new ResponseMessagesDTO(new Messages\LOGIN_ERROR_Message), exception: $e);
            case($e instanceof CreatorsExceptions\ENTITY_EXISTS_EXCEPTION):
                return ResponseProvider::render(new ResponseMessagesDTO(new Messages\ENTITY_EXIST_Message), exception: $e);
            case($e instanceof CreatorsExceptions\MICROSERVICE_EXCEPTION):
                return ResponseProvider::render(new ResponseMessagesDTO(new Messages\MICROSERVICE_EXCEPTION_Message), exception: $e);
            case($e instanceof CreatorsExceptions\BAD_QUERY_STRING_EXCEPTION):
            case($e instanceof RelationNotFoundException):
                return ResponseProvider::render(new ResponseMessagesDTO(new Messages\BAD_QUERY_STRING_Message($e->getMessage())), exception: $e);
            default:
                return ResponseProvider::render(new ResponseMessagesDTO(new Messages\INTERNAL_SERVER_ERROR_Message));
        }
    }
}
