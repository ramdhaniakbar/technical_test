<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function signin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        $token = JWTAuth::attempt($credentials, ['secret' => config('jwt.secret')]);

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'access-token' => $token
        ]);
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        function genRandomNumber()
        {
            $characters = '0123456789';
            $charactersLength = strlen($characters);
            $randomNumber = '';

            for ($i = 0; $i < 5; $i++) {
                $randomNumber .= $characters[rand(0, $charactersLength - 1)];
            }

            return $randomNumber;
        }

        $genRand = genRandomNumber();

        $user = new User();
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->npp = $genRand;
        $user->npp_supervisor = '-';
        $user->save();

        $token = auth()->attempt(['email' => $request->email, 'password' => $request->password]);

        return response()->json(['access-token' => $token], 201);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Successfully logged out']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout'], 500);
        }
    }

    public function checkToken(Request $request)
    {
        return "OKe Valid";
    }
}
