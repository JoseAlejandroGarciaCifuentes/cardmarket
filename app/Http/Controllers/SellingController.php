<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Selling;

use App\Http\Helpers\MyJWT;

use \Firebase\JWT\JWT;

class SellingController extends Controller
{
	/**
	 * Comprueba el rol antes de ejecutar este método, si no eres admin pero estás logged se pone una carta a vender asociado al id
	 * con un precio total y una cantidad
	 */
    public function startSelling(Request $request, $id){

		$response = "";
		$data = $request->getContent();
		$data = json_decode($data);

		$key = MyJWT::getKey();
		$headers = getallheaders();
		$separating_bearer = explode(" ", $headers['Authorization']);
		$token = $separating_bearer[1];
		$decoded = JWT::decode($token, $key, array('HS256'));

		if($data){

			$selling = new Selling();

			$selling->card_id = $id;
			$selling->user_id = $decoded->id;

			$selling->total_price = $data->total_price;
			$selling->quantity = $data->quantity;

			try{
				$selling->save();
				$response = "Carta/s en venta";
			}catch(\Exception $e){
				$response = $e->getMessage();
			}
		}

		return response()->json($response);

	}
}
