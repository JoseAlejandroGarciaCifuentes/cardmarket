<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Card;
use App\Models\Collection;
use App\Models\cardCollection;

class CardController extends Controller
{
    public function sellingsByPrice($name){

		$cards = Card::where('name','like','%'.$name.'%')->get();
		$response = [];
		
		foreach ($cards as $card) {
			foreach ($card->user as $seller) {
				$response[] = [
					"Card Name" => $card->name,
					"Quantity" => $seller->pivot->quantity,
					"Total Price" => $seller->pivot->total_price,
					"Seller" => $seller->username
				];
			}	
        } 

		return response()->json($response);
	}

	public function registerCard(Request $request){

		$response = [];
		$data = $request->getContent();
		$data = json_decode($data);

		$card = new Card();
		
		if($data){

			$card->name = $data->name;
			$card->description = $data->description;
			$card->admin_id = $data->admin_id;
			//USER ID TIENE QUE SER RECOGIDO DE TOKEN
			try{
				$card->save();
				$response[]="carta añadido";
            }catch(\Exception $e){
                $response = $e->getMessage();
			}

			$collection = Collection::where('name', $data->collection)->get()->first();
			
			$cardCollection = new CardCollection();

			if($collection){
				$cardCollection->card_id = $card->id;
				$cardCollection->collection_id = $collection->id;

				try{
					$cardCollection->save();
					$response[]="cardCollection añadido";
				}catch(\Exception $e){
					$response = $e->getMessage();
				}

			}else{
				$collection = new Collection();
				$collection->name = $data->collection;
				$collection->user_id = $data->user_id;
				//USER ID TIENE QUE SER RECOGIDO DE TOKEN

				try{
					$collection->save();
					$response[]="cardCollection añadido, nueva colección";
				}catch(\Exception $e){
					$response = $e->getMessage();
				}

				$cardCollection->card_id = $card->id;
				$cardCollection->collection_id = $collection->id;

				try{
					$cardCollection->save();
					$response[]="cardCollection añadido, nueva colección";
				}catch(\Exception $e){
					$response = $e->getMessage();
				}
				
			}
			
		}else{
			$response="Datos incorrectos";
		}

		return response($response);

	}

	public function cardsByName($name){

		$cards = Card::where('name','like','%'.$name.'%')->get();
		$response = [];
		
		for ($i=0; $i <count($cards) ; $i++) { 

			$response[$i] = [
				"Id" => $cards[$i]->id,
				"Card Name" => $cards[$i]->name
			];

			//count($cards[$j]->collection);

			for ($j=0; $j <count($cards[$i]->collection) ; $j++) { 

				$response[$i][$j]['Collection'] = $cards[$i]->collection[$j]->name;
				$response[$i][$j]['Collection Description'] = $cards[$i]->collection[$j]->description;
			}
			$response[$i]['uploaded by'] = $cards[$i]->admin->username;
		}	
        
		return response()->json($response);
	}


}
