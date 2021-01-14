<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTokenIsValid
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
        $data = $request->getContent();
        $data = json_decode($data);

        if ($data->token !== "123") {
            abort(403, "¡No tienes permiso!");
        }
        return $next($request);
    }
}
