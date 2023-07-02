<?php

namespace App\Http\Controllers\api\auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{


    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email|email',
            'password' => 'required|string',
        ]);
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);
        $token = $user->createToken('mytoken')->plainTextToken;
        $response = [
            'message' => "You are registered successfully.",
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    /** Login for passport authentication **/
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $accessToken = $user->createToken('MyApp')->accessToken;

            return response()->json([
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
            ]);
        } else {
            return response()->json([
                'error' => 'Unauthorized',
            ], 401);
        }
    }



    /** Login for sanctum authentication **/
    // public function login(Request $request)
    // {
    //     $fields = $request->validate([
    //         'email' => 'required',
    //         'password' => 'required|string',
    //     ]);

    //     $user = User::where('email', $fields['email'])->first();
    //     $token = $user->createToken('mytoken')->plainTextToken;

    //     //check password
    //     if (!$user || !Hash::check($fields['password'], $user->password)) {

    //         return response([
    //             'message' => "Wrong Password or Email.",
    //         ], 401);
    //     }

    //     $response = [
    //         'message' => "Successfully Login.",
    //         'user' => $user,
    //         'token' => $token
    //     ];

    //     return response($response, 201);
    // }
}
