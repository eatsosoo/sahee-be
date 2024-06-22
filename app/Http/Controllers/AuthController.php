<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'result' => false, // add this line
                'data' => ['message' => 'Email hoặc mật khẩu không đúng!']
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'result' => true, // add this line
            'data' => [
                'message' => 'Successfully logged in',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'result' => true, // add this line
            'data' => ['message' => 'Successfully logged out']
        ]);
    }

    public function checkBearerToken(Request $request)
    {
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return response()->json([
                'result' => false,
                'data' => ['message' => 'Bearer token is missing']
            ], 401);
        }

        $accessToken = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $bearerToken))
            ->first();

        if (!$accessToken) {
            return response()->json([
                'result' => false,
                'data' => ['message' => 'Invalid bearer token']
            ], 401);
        }

        return response()->json([
            'result' => true,
            'data' => ['message' => 'Bearer token is valid']
        ]);
    }
}
