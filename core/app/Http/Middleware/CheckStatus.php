<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->access_type == 'general' || Auth::user()->access_type == 'member') {
            $user = Auth()->user();
            if ($user->status  && $user->ev  && $user->sv  && $user->tv) {
                //                dd('okay');
                return $next($request);
            } else {
                // dd('hie');
                return redirect()->route('user.authorization');
            }
        }

        return $next($request);
    }
}
