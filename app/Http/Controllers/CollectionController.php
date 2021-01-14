<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Card;
use App\Models\Collection;
use App\Models\cardCollection;

class CollectionController extends Controller
{
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

    public function editCollection(Request $request){


    }

    public function createCollection(Request $request){


    }
}
