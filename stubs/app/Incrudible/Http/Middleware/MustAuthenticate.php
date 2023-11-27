<?php

namespace App\Incrudible\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MustAuthenticate
{
    /**
     * Answer to unauthorized access request.
     *
     * @param [type] $request [description]
     *
     * @return [type] [description]
     */
    private function respondToUnauthorizedRequest(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response(null, 401);
        } else {
            return redirect()->to(incrudible_route('auth.login'));
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = "incrudible")
    {
        if (!auth()->guard($guard)->check()) {
            return $this->respondToUnauthorizedRequest($request);
        }
        return $next($request);
    }
}
