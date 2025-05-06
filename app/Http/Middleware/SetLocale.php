<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // 1) Handmatig gekozen taal in sessie
        if ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        }
        // 2) Anders: Accept-Language header (eerste waarde)
        else {
            $locale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
            if (! in_array($locale, ['nl', 'en'])) {
                $locale = config('app.locale'); // val terug op NL
            }
        }

        App::setLocale($locale);

        return $next($request);
    }
}
