<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\RegisterMail;
use Illuminate\Http\Request;
use App\Http\Requests\AuthUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // dd(123);
        // Validate the request data using the AuthUser request class
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "email" => "required|email|string|max:255|unique:users",
            "password" => "required|string|min:8|confirmed",
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false , 'errors' => $validator->errors()], 200 );
        }
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
        ]);

        Mail::to($user)->send(new RegisterMail($user));

        return response()->json(['message' => 'User Register'] , 200);
    }



    public function login(Request $request)
    {
        $login = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!Hash::check($request->password , $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 200);
    }

}
