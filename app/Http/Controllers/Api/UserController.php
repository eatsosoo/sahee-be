<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function loginUser(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return ApiResponse::v1()->withStatusCode(401)->report(false, $validator->errors());
        }

        if (Auth::attempt($request->all())) {

            $user = Auth::user();

            $success = $user->createToken('MyApp')->plainTextToken;
            return ApiResponse::v1()->report(true, $success, 'token');
        }


        return ApiResponse::v1()->withStatusCode(400)->report(false, 'email or password wrong');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function userDetails(): Response
    {
        if (Auth::check()) {

            $user = Auth::user();

            return Response(['data' => $user], 200);
        }

        return Response(['data' => 'Unauthorized'], 401);
    }

    /**
     * Display the specified resource.
     */
    public function logout(): Response
    {
        $user = Auth::user();

        $user->currentAccessToken()->delete();

        return ApiResponse::v1()->report(true, 'User Logout successfully.');
    }
}
