<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|string',
            'password' => 'required|string',
        ];

        $messages = [
            'email.required' => 'Email tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->first()], 422);
        }

        $valid = User::where('email', $request->email)->first();
        if (!$valid || !Hash::check($request->password, $valid->password)) {
            return response(['errors' => 'Email and Password does not match!'], 401);
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response(['errors' => 'Invalid credentials'], 401);
            }
        } catch (Exception $e) {
            return response(['errors' => 'Could not create token ('.$e.')'], 400);
        }

        $user = [
            'id' => $valid->id,
            'name' => $valid->name,
            'username' => $valid->username,
            'email' => $valid->email,
        ];

        return response()->json(['user' => $user, 'token' => $token], 200);
    }

    public function register(Request $request)
    {
        $rules = [
            'username' => 'required|unique:users',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|string|same:password',
            'name' => 'required|string',
            'email' => 'required|string|email|unique',
        ];

        $validator = Validator::make($request->all(), $rules);


        if($validator->fails()){
            // return response()->json($validator->errors(), 403);
            return response(['errors' => $validator->errors()->first()], 422);
        }

        $fields = $request->all();

       User::create([
            'name' => $fields['name'],
            'username' => $fields['username'],
            'password' => Hash::make($fields['password']),
            'email' => $fields['email'],
        ]);

        return response()->json(['message' => 'Register telah berhasil!'], 201);
    }

    public function refreshToken()
    {
        $token = JWTAuth::getToken();

        if (!$token) {
            return response(['errors' => 'Token not provided'], 401);
        }

        try {
            $newToken = JWTAuth::refresh($token);
        } catch (Exception $e) {
            return response(['errors' => 'Token refresh failed'], 401);
        }

        $user = JWTAuth::toUser($newToken);

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
        ];

        return response()->json(['user' => $userData, 'token' => $newToken], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //
    }
}
