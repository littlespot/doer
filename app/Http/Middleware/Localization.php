<?php

namespace Zoomov\Http\Middleware;

use Closure;
use Session;
use App;
use Illuminate\Support\Facades\Auth;
use Config;

class Localization
{
    /**
     *
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = session('locale');

        if(is_null($locale)){
            $locale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);

            if ($locale != 'zh' && $locale != 'en') {
                $locale = config('app.fallback_locale');
            }

            session(['locale'=>$locale]);
        }

        App::setLocale($locale);
        return $next($request);
    }
}
