<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Http\Helpers\MyJWT;
use \Firebase\JWT\JWT;

class AuthNonAdmin
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

        $key = MyJWT::getKey();

        $headers = getallheaders();
        
        if(array_key_exists('Authorization', $headers)){
            $separating_bearer = explode(" ", $headers['Authorization']);
            $token = $separating_bearer[1];
            
            $decoded = JWT::decode($token, $key, array('HS256'));

            if($decoded){

                if($decoded->role !== ADMIN){
                    return $next($request);
                }else{
                    abort(403, "¡Usted no está permitido aquí!");
                }

            }else{
                abort(403, "¡Token vacío!");
            }
        }else{
            abort(403, "¡No has pasado token!");
        }
    }
}
