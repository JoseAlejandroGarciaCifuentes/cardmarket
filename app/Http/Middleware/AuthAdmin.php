<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;

class AuthAdmin
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
        define("ADMIN","Administrator");

        if($request->token){
            $user = User::where('api_token',$request->token)->first();

            if($user){
                if($user->role === ADMIN){
                    return $next($request);
                }else{
                    abort(403, "¡Usted no está permitido aquí!");
                }
            }else{
                abort(403, "¡Token erróneo!");
            }

        }else{
            abort(403, "¡Token vacío!");
        }
    }
}
