<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        }

        else {
            $locale = substr($request->server('HTTP_ACCEPT_LANGUAGE') ?? 'nl', 0, 2);
            if (! in_array($locale, ['nl', 'en'])) {
                $locale = config('app.locale');
            }
        }

        App::setLocale($locale);

        return $next($request);
    }
}
