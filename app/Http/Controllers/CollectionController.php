<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Card;
use App\Models\Collection;
use App\Models\cardCollection;
use App\Models\User;

use App\Http\Helpers\MyJWT;

use \Firebase\JWT\JWT;

class CollectionController extends Controller
{
	/**
	 * Asigna una carta a una colección
	 */
    public function assignCard(Request $request){

        $response = [];
		$data = $request->getContent();
		$data = json_decode($data);
		
        if($data&&Card::find($data->card)&&Collection::find($data->collection)){

            $cardCollection = new CardCollection();
            $cardCollection->card_id = $data->card;
            $cardCollection->collection_id = $data->collection;

            try{
                $cardCollection->save();
                $response = "OK";
            }catch(\Exception $e){
                $response = $e->getMessage();
            }

        }

        return response($response);
    }

	/**
	 * Edita/actualiza una colección
	 */
    public function editCollection(Request $request, $id){

        $response = "";

		$collection = Collection::find($id);

		if($collection){

			$data = $request->getContent();
			$data = json_decode($data);

			if($data){

				$collection->name = (isset($data->name) ? $data->name: $collection->name);
                $collection->symbol = (isset($data->symbol) ? $data->symbol: $collection->symbol);
                $collection->creation_date = (isset($data->creation_date) ? $data->creation_date: $collection->creation_date);

				try{
					$collection->save();
					$response = "Colección editada";
				}catch(\Exception $e){
					$response = $e->getMessage();
				}
			}
			
		}else{
			$response = "No existe dicha colección";
		}
		
		return response($response);
    }

	/**
	 * Crea/registra una nueva colección además de recibir un nombre de carta que comprobará la existencia de esta
	 * en caso de existir se asocia y en caso de que no se crea.
	 */
    public function registerCollection(Request $request){

        $response = [];
		$data = $request->getContent();
		$data = json_decode($data);

		$key = MyJWT::getKey();
		$headers = getallheaders();
		$separating_bearer = explode(" ", $headers['Authorization']);
		$token = $separating_bearer[1];
		$decoded = JWT::decode($token, $key, array('HS256'));

		$collection = new Collection();
		
		if($data){

			$collection->name = $data->name;
			$collection->symbol = $data->symbol;
			$collection->creation_date = $data->creation_date;
			$collection->admin_id = $decoded->id;

			try{
				$collection->save();
				$response[]="colección añadida";
            }catch(\Exception $e){
                $response = $e->getMessage();
			}

			$card = Card::where('name', $data->card)->get()->first();
			
			$cardCollection = new CardCollection();

			if($card){
				$cardCollection->card_id = $card->id;
				$cardCollection->collection_id = $collection->id;

				try{
					$cardCollection->save();
					$response[]="cardCollection añadido";
				}catch(\Exception $e){
					$response = $e->getMessage();
				}

			}else{
				$card = new Card();
				$card->name = $data->card;
				$card->admin_id = $decoded->id;

				try{
					$card->save();
					$response[]="carta añadida";
				}catch(\Exception $e){
					$response = $e->getMessage();
				}

				$cardCollection->card_id = $card->id;
				$cardCollection->collection_id = $collection->id;

				try{
					$cardCollection->save();
					$response[]="cardCollection añadido";
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
	public function getCollections(){

		$collections = Collection::all();
		$response = [];

		if(!$collections->isEmpty()){
		
			for ($i=0; $i <count($collections) ; $i++) { 

				$response[$i] = [
					"name" => $collections[$i]->name,
					"symbol" => $collections[$i]->symbol,
					"creation_date" => $collections[$i]->creation_date
				];
				
				/*for ($j=0; $j <count($collections[$i]->card); $j++) { 

					$response[$i][$j]['cards'] = $collections[$i]->card[$j]->name;
					
				}*/
				
				$response[$i]['userWhoPostedIt'] = $collections[$i]->admin->username;
			}	
		}else{
			$response = "No collections";
		}
		return response()->json($response);
	}
}
