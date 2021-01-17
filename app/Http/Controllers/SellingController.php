<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Selling;

use App\Http\Helpers\MyJWT;

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
		$decoded = JWT::decode($headers['api_token'], $key, array('HS256'));

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

		return response($response);

	}
}
