<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB; 
use Illuminate\Http\Request;


class ListsController extends Controller{

//create a new list list url:create
	public function createList(Request $request){
		 $this->validate($request, [
	       'list' => 'required',
	       'role' => 'required',
	       'user_id'=>'required'
        ]);

		 $list = $request->input('list');
		 $role = $request->input('role');
		 $user = $request->input('user_id');
		DB::table('lists')->insert(['list_name' => $list, 'role' => $role, 'user_id' => $user]);
		return response()->json(['status' => 'success'],200);
	}

//add an item to a list url:/additem
	public function addItemToList(Request $request){
		$this->validate($request, [
      	 'item_name' => 'required',
      	 'list_id' => 'required',
     	 'quantity'=>'required'
        ]);
		 $date = date('d-m-y His');
		 $cost = $request->input('cost');
		 $item_name = $request->input('item_name');
		 $list_id = $request->input('list_id');
		 $location = $request->input('location');
		 $quantity = $request->input('quantity');
		 $user_id =  $request->input('user_id');
		DB::table('lists_data')->insert(['cost' => $cost, 'item_name' => $item_name, 'list_id' => $list_id, 
			'location' => $location, 'quantity'=>$quantity ,'user_id'=>$user_id ,'created_at'=>$date]);
		return response()->json(['status' => 'success'],200);

	}

	//check / uncheck item off list url:/checkoff
	public function checkOffList(Request $request){

		$this->validate($request, ['list_id','item_id','checked']);

		$list = $request->input('list_id');
		$item = $request->input('item_id');
		$checked = $request->input('checked');
		DB::table('lists_data')->where([['list_id',$list],['id',$item]])->update(['checked'=>$checked]);
		return response()->json(['status' => 'success'],200);

	}

	//display list of lists for user url:/getlist
	public function showList(Request $request){

		$this->validate($request, ['user']);
		$user = $request->input('user');
		$lists = DB::table('lists')->where([['user_id',$user]])->get();
		return response()->json(['lists' => $lists],200);

	}

	//display list data for current list for user url:/getlist
	public function showListData(Request $request){
		$this->validate($request, ['user','listid']);
		$user = $request->input('user');
		$listid=$request->input('listid');
			
			$data = DB::table('lists_data')
			->join('lists','lists.id','=','lists_data.list_id')
			->select('lists.list_name','lists_data.item_name','lists_data.quantity',
					 'lists_data.cost','lists_data.location','lists_data.checked','lists_data.created_at')
			->where([['lists.user_id','=',$user],['lists_data.list_id','=',$listid]])
			->get();
			
			return response()->json(['data' => $data],200);
	}


	public function showGroupList(){

			$this->validate($request, ['user']);
		$user = $request->input('user');
		$lists = DB::table('lists')->where([['user_id',$user],['role','<','10']])->get();
		return response()->json(['lists' => $lists],200);


	}


	public function removeItemFromList(Request $request){

		$this->validate($request, ['user','listid','itemid']);
		$user = $request->input('user');
		$listid = $request->input('listid');
		$itemid = $request->input('itemid');
		
		if($this->checkAccess($user,$listid,$itemid)){
			
			DB::table('lists_data')->where('id', '=', $itemid)->delete();

		}
	}
	
	public function removeList(Request $request){

		$this->validate($request,['user','listid']);
		$user = $request->input('user');
		$listid = $request->input('listid');

		if($this->checkAccess($user,$listid)){

			DB::table('lists_data')->where('list_id','=',$listid)->delete();
			DB::table('lists')->where('list_id','=',$listid)->delete();

		}
	}


	public function updateListItem(Request $request){

		$user = $request->input('user');
		$listid = $request->input('listid');
		$itemid = $request->input('itemid');
		$column = $request->input('column');
		$value = $request->input('value');
		$this->validate($request, ['user','listid','itemid']);
		
		if($this->checkAccess($user,$listid,$itemid)){

			DB::table('lists_data')->where([['id',$itemid]])->update([$column=>$value]);

		}
		return response()->json(['status'=>'success'],200);
	}

	

	public function checkAccess($userid,$listid,$itemid="",$returnvals=false){

		$prepared = ['user'=>$userid,'listid'=>$listid];
		if($itemid!=""){

			$itemidval="and lists_data.id =:itemid ";
			$prepared['itemid'] =  $itemid; 
		}

		$data = DB::select(DB::raw("select lists_data.id,lists.list_name,lists_data.item_name,lists_data.quantity, 
			lists_data.cost,lists_data.location,lists_data.checked,lists_data.created_at 
			from lists_data
		join lists on lists.id = lists_data.list_id 
		where lists.user_id = :user or role = '10'  
		and lists.id = :listid ".$itemidval),$prepared);

		if(count($data)>0 ){
			if ($returnVals){
				return $data;
			}	
		return true;
		}
	 return false;
	}



}