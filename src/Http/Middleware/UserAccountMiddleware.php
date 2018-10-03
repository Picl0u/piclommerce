<?php

namespace Piclou\Piclommerce\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserAccountMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if(auth()->check()) {
            if(auth()->user()->role == 'user') {
                return $next($request);
            }
        }
        session()->flash('error',__('piclommerce::web.user_permission_error'));
        return redirect()->route('login');
    }
}
