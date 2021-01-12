<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Card;

class CardController extends Controller
{
    public function cardsByPrice($name){

		$cards = Card::where('name','like','%'.$name.'%')->get();
		$response = [];
		
		foreach ($cards as $card) {
			foreach ($card->user as $data) {
				$response[] = [
					"Card Name" => $card->name,
					"Quantity" => $data->pivot->quantity,
					"Total Price" => $data->pivot->total_price,
					"Seller" => $data->name
				];
			}	
        } 

		return response()->json($response);
	}
}
