<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Helpers\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\User;

class UserController extends Controller
{
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

        return response($response);
        
    }

    public function login(Request $request){

        $response = "";
        $token = new Token();
		$data = $request->getContent();
        $data = json_decode($data);
        
        $user = User::where('username', $data->username)->get()->first();
        
		if($data){

            if (Hash::check($data->password, $user->password)) { 
                $response = "Login correcto";
                $user->api_token = $token->encode($data->username.now());
                try{
                    $user->save();
                }catch(\Exception $e){
                    $response = $e->getMessage();
                }

                //AQUI TIENE QUE PASAR ALGO CON EL TOKEN ¿?

            }else{
                $response = "Usuario o contraseña no coinciden";
            }

        }

        return response($response);

    }

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

    public function makeAdmin($id){

        $response = "";
        //define("ADMIN","Administrator");
        
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
