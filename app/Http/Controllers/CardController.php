<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Card;
use App\Models\Collection;
use App\Models\cardCollection;
use App\Models\User;

use \Firebase\JWT\JWT;
use App\Http\Helpers\MyJWT;

class CardController extends Controller
{
	/**
	 * Devuelve las cartas en venta ordenadas por precio y nombre
	 */
    public function sellingsByPrice($name){

		$cards = Card::where('name','like','%'.$name.'%')->get();
		$response = [];
		
		if(count($cards)>0){
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
		}else{
			$response = "Ninguna carta coincide con el nombre introducido";
		}

		return response()->json($response);
	}

	/**
	 * Crea/registra una nueva carta además de recibir un nombre de colección que comprobará la existencia de esta
	 * en caso de existir se asocia y en caso de que no se crea.
	 */
	public function registerCard(Request $request){

		$response = [];
		$data = $request->getContent();
		$data = json_decode($data);

		$key = MyJWT::getKey();
		$headers = getallheaders();
		$decoded = JWT::decode($headers['api_token'], $key, array('HS256'));
		
		$card = new Card();

		if($data){

			$card->name = $data->name;
			$card->description = $data->description;
			$card->admin_id = $decoded->id;

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
				$collection->admin_id = $decoded->id;

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

		return response()->json($response);
		
	}
	/**
	 * Devuelve las cartas en venta ordenadas por nombre
	 */
	public function cardsByName($name){

		$cards = Card::where('name','like','%'.$name.'%')->get();
		$response = [];
		
		for ($i=0; $i <count($cards) ; $i++) { 

			$response[$i] = [
				"Id" => $cards[$i]->id,
				"Card Name" => $cards[$i]->name,
				"Card Description" => $cards[$i]->description
			];

			for ($j=0; $j <count($cards[$i]->collection) ; $j++) { 

				$response[$i][$j]['Collection'] = $cards[$i]->collection[$j]->name;
				$response[$i][$j]['Collection symbol'] = $cards[$i]->collection[$j]->symbol;
			}

			$response[$i]['uploaded by'] = $cards[$i]->admin->username;
		}	
        
		return response()->json($response);
	}

	/**
	 * Permite editar una carta
	 */
	public function editCard(Request $request, $id){

        $response = "";

		$card = Card::find($id);

		if($card){

			$data = $request->getContent();
			$data = json_decode($data);

			if($data){

				$card->name = (isset($data->name) ? $data->name: $card->name);
                $card->description = (isset($data->description) ? $data->description: $card->description);

				try{
					$card->save();
					$response = "Carta editada";
				}catch(\Exception $e){
					$response = $e->getMessage();
				}
			}
			
		}else{
			$response = "No existe dicha carta";
		}
		
		return response($response);
	}
	
}
