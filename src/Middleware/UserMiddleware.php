<?php


namespace phpcommon\Middleware;

use Closure;

class UserMiddleware
{
    public function handle($request, Closure $next)
    {
        if ($request->header('user-uuid'))
            $request->attributes->add(['user_uuid' => $request->header('user-uuid')]);

        return $next($request);
    }
}
