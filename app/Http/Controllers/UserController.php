<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\User;

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
       $email = $request->input('email');
       $password = $request->input('password');

       $user = User::where('email', $email)->first();

       if (Hash::check($password, $user->password)) {
           $api_token = base64_encode(str_random(40));

           $user->update([
               'api_token' => $api_token
           ]);

           return response()->json([
               'success' => true,
               'message' => 'Login Success!',
               'data' => [
                   'user'      => $user,
                   'api_token' => $api_token
               ]
           ], 201);
       } else {
           return response()->json([
               'success' => false,
               'message' => 'Login Failed!',
               'data' => ''
           ], 400);
       }
     }

    //
}
