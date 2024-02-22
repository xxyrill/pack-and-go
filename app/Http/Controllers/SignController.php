<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignController extends Controller
{
    public function login(Request $request){
        $validate = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(!Auth::attempt($validate)){
            return response([
                'errors' => ['email'=>['Invalid Credentials']]
            ],400);
        }else{
            $user = Auth::user();
            $name = 'user-'.$user->id.'-token';
            $return = [
                'token' => $user->createToken($name)->plainTextToken,
                'type' => $user->type,
                'id' => $user->id
            ];
            return response($return);
        }
    }
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response([
            'message' => 'log out.'
        ]);
    }
}
