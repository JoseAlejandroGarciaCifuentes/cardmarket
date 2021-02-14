<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use \Firebase\JWT\JWT;

use App\Models\User;
use App\Http\Helpers\MyJWT;

class UserController extends Controller
{
    /**
     * Registra un usuario en base al body recibido
     */
    public function signUp(Request $request){

        $response = "";
		$data = $request->getContent();
        $data = json_decode($data);
        
		if($data){

            if($data->role !== "Administrator"){
                $user = new User();

                $user->username = $data->username;
                $user->email = $data->email;
                $user->password = Hash::make($data->password);
                $user->role = $data->role;
            
                try{
                    $user->save();
                    $response = "Añadido!";
                }catch(\Exception $e){
                    $response = $e->getMessage();
                }
            }else{
                $response = "Tu cuenta no ha sido creada, no puedes ponerte rol de Admin, prueba 'Individual' o 'Professional'";
            }

		}else{
			$response = "No has introducido un usuario válido";
		}
        //added for testing
        //$response = $user;
        

        return response()->json($response);
        
    }
    /**
     * Comprueba user y password con la base de datos, genera un token que contiene el id y el rol este se
     * guarda en la bbdd asociado a dicho user
     */
    public function login(Request $request){

        $response = "";
		$data = $request->getContent();
        $data = json_decode($data);
        
        $user = User::where('username', $data->username)->get()->first();
        if(!empty($user)){
            $payload = MyJWT::generatePayload($user);
            $key = MyJWT::getKey();

            $jwt = JWT::encode($payload, $key);
            
            if($data){

                if (Hash::check($data->password, $user->password)) { 

                    $response = $jwt;

                    try{
                        $user->save();
                    }catch(\Exception $e){
                        $response = $e->getMessage();
                    }

                }else{
                    $response = "600";
                }
            }
        }else{
            $response = "700";
        }

        return response()->json($response);

    }
    /**
     * Resetea la constraseña, genera una nueva aleatoria, la guarda en la bbdd y la devuelve en el response
     */
    public function restorePassword(Request $request){

        $response = "";
		$data = $request->getContent();
        $data = json_decode($data);

        $user = User::where('email', $data->email)->get()->first();

        if($data){

            $newRandomPassword = Str::random(60);
            $user->password = Hash::make($newRandomPassword);

            $response = $newRandomPassword;

            try{
                $user->save();
            }catch(\Exception $e){
                $response = $e->getMessage();
            }
        }else{
            $response = "Datos erroneos";
        }

        return response($response);

    }

    /**
     * Si el que hace la llamada está logged como Admin podrá hacer admin a otro usuario con este método.
     */
    public function makeAdmin($id){

        $response = "";
        
        $user = User::find($id);

        if($user && $user->role!==ADMIN){//COMPROBAR SI EL USER DEL TOKEN ES ADMIN

            $user->role = ADMIN;

            try{
                $user->save();
                $response = "El usuario ".$user->username." ahora es administrador";
            }catch(\Exception $e){
                $response = $e->getMessage();
            }

        }else{
            $response = "No se ha encontrado dicho user o ya es administrador";
        }

        return response($response);
    }
}
