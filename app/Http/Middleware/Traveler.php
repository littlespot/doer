<?php

namespace Zoomov\Http\Middleware;

use Closure;
use Session;

class Traveler
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->session()->token();

        if (!is_string($token) || is_null(Session::get($token)))
        {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('travel');
            }
        }

        return $next($request);
    }
}
