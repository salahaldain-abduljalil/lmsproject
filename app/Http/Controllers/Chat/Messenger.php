<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Messenger extends Controller
{
    public function index(){

        return view('Messenger.Layout.app');
    }
  //search user profile.
    public function search(Request $request){

        $getRecords = null;
        $input = $request['query'];
        $records = User::where('id','!=',Auth::user()->id)
        ->where('name','LIKE',"%{$input}%")->orWhere('user_name','LIKE',"%{$input}%")
        ->paginate(10);

        if($records->total() < 1){

            $getRecords .= "<p class='text-center'>Nothing to show.</p>"; //for the search part.
        }


        foreach($records as $record){

         $getRecords .= view('Messenger.Components.search-item',compact('record'))->render();

        }

        return response()->json([
            'records'=> $getRecords,
            'last_page' => $records->lastPage(),//for the pagination.
        ]);
    }

    public function fetchIdinfo(Request $request){

        $fetch = User::where('id',$request['id'])->first();
        return response()->json([
            'fetch'=>$fetch
        ]);
    }
}
