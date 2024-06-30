<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function AddToWishList(Request $request , $course_id){
        if (Auth::check()) {
            $exists = Wishlist::where('user_id',Auth::id())->where('course_id',$course_id)->first();

            if (!$exists) {
             Wishlist::insert([
                 'user_id' => Auth::id(),
                 'course_id' => $course_id,
                 'created_at' => Carbon::now(),
             ]);
             return response()->json(['success' => 'Successfully Added on your Wishlist']);
            }else {
             return response()->json(['error' => 'This Product Has Already on your withlist']);
            }

         }else{
             return response()->json(['error' => 'At First Login Your Account']);
         }
    }

    public function AllWishlist(){

        return view('frontend.wishlist.all_wishlist');

    }

    public function GetWishlistCourse(){

        $wishlist = Wishlist::with('course')->where('user_id',Auth::id())->latest()->get();
        $wishqty = Wishlist::count();

        return response()->json(['wishlist' => $wishlist , 'wishqty' => $wishqty]);

    }
    public function RemoveWishlist($id){

        Wishlist::where('user_id',Auth::id())->where('id',$id)->delete();
        return response()->json(['success' => 'Successfully Course Remove']);

    }
}
