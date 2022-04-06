<?php


namespace phpcommon\Middleware;

use Closure;

class AccessLevelMiddleware
{
    public function handle($request, Closure $next)
    {
        if ($request->header('access-level'))
            $request->attributes->add(['access_level' => $request->header('access-level')]);
        else
            $request->attributes->add(['access_level' => 0]);

        return $next($request);
    }
}
