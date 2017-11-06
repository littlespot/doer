<?php

namespace Zoomov\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Professional
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = 'user')
    {
        if (Auth::guard($guard)->guest() || !Auth::user()->professional) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('inaccessible');
            }
        }

        return $next($request);
    }
}
