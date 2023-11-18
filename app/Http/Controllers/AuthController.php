<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){
        $attrs = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);
        $user = User::create([
            'name' => $attrs['name'],
            'email' => $attrs['email'],
            'password' => bcrypt($attrs['password']),
        ]);

        // return user and token 
        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken
        ],200);
    }
    // login
    public function login(Request $request){
        $attrs = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        
        if (!Auth::attempt($attrs)) {
            return response([
                'message' => 'invalid credentials'
            ],403);
        }

        // return user and token 
        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken
        ],200);
    }

    // logout
    public function logout(){
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'logout Successfully'
        ],200);
    }

    // get user details
    public function user(){
        return response([
            'user' => auth()->user()
        ],200);
    }

    public function update(Request $request){
        $attrs = $request->validate([
            'name' => 'required|string',
        ]);
        $image = $this->saveImage($request->image, 'profiles');

        auth()->user()->update([
            'name' => $attrs['name'],
            'image' => $image
        ]);

        return response([
            'message' => 'User Updated',
            'user' => auth()->user()
        ],200);
    }
}
