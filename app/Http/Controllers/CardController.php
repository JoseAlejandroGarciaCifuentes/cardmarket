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

		if(isset($data->name) && isset($data->description) && isset($data->collection)){

			$card->name = $data->name;
			$card->description = $data->description;
			$card->admin_id = $decoded->id;

			try{
				$card->save();
				$response[]="carta añadida";
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
	/*public function cardsByName($name){

		$cards = Card::where('name','like','%'.$name.'%')->get();
		$response = [];

		if(!$cards->isEmpty()){
		
			for ($i=0; $i <count($cards) ; $i++) { 

				$response[$i] = [
					"id" => $cards[$i]->id,
					"name" => $cards[$i]->name,
					"description" => $cards[$i]->description
				];
				
				for ($j=0; $j <count($cards[$i]->collection); $j++) { 

					$response[$i][$j]['collection'] = $cards[$i]->collection[$j]->name;
					$response[$i][$j]['collectionSymbol'] = $cards[$i]->collection[$j]->symbol;

				}
				
				$response[$i]['userWhoPostedIt'] = $cards[$i]->admin->username;
			}	
		}else{
			$response = "No cards";
		}
		return response()->json($response);
	}*/

	/**
	 * Devuelve las cartas en venta ordenadas por nombre
	 */
	public function cardsByName($name){

		$card = Card::where('name','like','%'.$name.'%')->get()->first();
		$response = [];

		if(!empty($card)){
		
			$response = [
				"id" => $card->id,
				"name" => $card->name,
				"description" => $card->description
			];
			
			for ($i=0; $i <count($card->collection); $j++) { 

				$response[$i][$j]['name'] = $card->collection[$i]->name;
				$response[$i][$j]['symbol'] = $card->collection[$i]->symbol;

			}
			
			$response['userWhoPostedIt'] = $card->admin->username;
			
		}else{
			$response = "No cards";
		}
		return response()->json($response);
	}

	/**
	 * Permite editar una carta
	 */
	public function editCard(Request $request, $id){

		$response = [];
		
		$data = $request->getContent();
		$data = json_decode($data);

		if($data){

			$card = Card::find($id);
			$response[] = "El JSON pasado es correcto";

			if($card){

				$card->name = (isset($data->name) ? $data->name: $card->name);
                $card->description = (isset($data->description) ? $data->description: $card->description);
				$response[] ="La carta existe";
				
				try{

					$card->save();
					$response[] = "La carta se ha guardado";

				}catch(\Exception $e){

					$response[] = $e->getMessage();
				}
			}else{
				$response[] = "No existe dicha carta";
			}
		}else{
			$response[] = "JSON inválido";
		}
		
		return response()->json($response);
	}

	/**
	 * Devuelve las cartas en venta ordenadas por nombre
	 */
	public function getCards(){

		$cards = Card::all();
		$response = [];

		if(!$cards->isEmpty()){
		
			for ($i=0; $i <count($cards) ; $i++) { 

				$response[$i] = [
					"id" => $cards[$i]->id,
					"name" => $cards[$i]->name,
					"description" => $cards[$i]->description
				];
				
				for ($j=0; $j <count($cards[$i]->collection); $j++) { 

					$response[$i][$j]['collection'] = $cards[$i]->collection[$j]->name;
					$response[$i][$j]['collectionSymbol'] = $cards[$i]->collection[$j]->symbol;

				}
				
				$response[$i]['userWhoPostedIt'] = $cards[$i]->admin->username;
			}	
		}else{
			$response = "No cards";
		}
		return response()->json($response);
	}
	
}
