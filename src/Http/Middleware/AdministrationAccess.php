<?php

namespace Piclou\Piclommerce\Http\Middleware;

use Closure;

class AdministrationAccess
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
        if(auth()->check()) {
            if(auth()->user()->role == config('piclommerce.adminRole')) {
                return $next($request);
            }
        }
        session()->flash('error',__('piclommerce::admin.permission_error'));
        return redirect()->route('admin.login');
    }
}
