<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Selling;

class SellingController extends Controller
{
    public function startSelling(Request $request, $id){

		$response = "";
		$data = $request->getContent();
		$data = json_decode($data);

		if($data){

			$selling = new Selling();

			$selling->card_id = $id;
			$selling->user_id = $data->user_id;
			//USER ID TIENE QUE SER RECOGIDO DE TOKEN

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
