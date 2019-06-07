<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function login(Request $request)
     {
         $this->validate($request, [
         'email' => 'required',
         'password' => 'required'
          ]);

       if(Hash::check($request->input('password'), 'testLumen'){
            $apikey = base64_encode(str_random(40));

            return response()->json(['status' => 'success','api_key' => $apikey]);
        }else{
            return response()->json(['status' => 'fail'],401);
        }
     }

    //
}
