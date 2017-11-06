<?php

namespace Zoomov\Http\Middleware;

use Closure;
use Session;
use App;
use Config;
use Auth;

use Illuminate\Cookie\Middleware\EncryptCookies as BaseEncrypter;

class SetActive extends BaseEncrypter
{
    public function handle($request, Closure $next, $guard = null)
    {
        if (is_null(Auth::guard($guard)->user()->active)) {
            return redirect('password/reset');
        }

        return $next($request);
    }
}
