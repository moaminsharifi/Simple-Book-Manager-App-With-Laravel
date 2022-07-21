<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       
        if (!auth()->check() ) {
            if (! $request->expectsJson()) {
                
                return response()->json(401);
            }
            return response('',401);

            
        }
        if (!auth()->user()->is_admin) {
            if (! $request->expectsJson()) {
                
                return response()->json(403);
            }
            return response('',403);
        }
        return $next($request);
    }
}
