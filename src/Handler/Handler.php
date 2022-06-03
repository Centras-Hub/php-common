<?php

namespace phpcommon\Handler;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use phpcommon\http\Messages;
use phpcommon\http\ResponseMessagesDTO;
use phpcommon\http\ResponseProvider;
use Symfony\Component\HttpKernel\Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;


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
        if(in_array($e::class, array_keys(ExceptionsWithOwnHandlingMap::getMap()))) {
            return ExceptionsWithOwnHandlingMap::getMap()[$e::class]($e);
        }
         $exceptionIsHandledWithMessage = false;
         $message = '';
         foreach(ExceptionMessageMap::$map as $key => $exceptionRenderValue) {

             if(in_array($e::class, $exceptionRenderValue)) {
                 $message = $key;
                 $exceptionIsHandledWithMessage = true;
                 break;
             }
         }

         return ResponseProvider::render(new ResponseMessagesDTO(  ($exceptionIsHandledWithMessage) ? (new $message) : new Messages\INTERNAL_SERVER_ERROR_Message), exception: $e);

    }
}
