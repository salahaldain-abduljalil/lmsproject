<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Messenger extends Controller
{
    public function index(){

        return view('Messenger.Layout.app');
    }
}
