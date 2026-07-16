<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
        ]);

        return $this->successResponse(
            new UserResource($user),
            'User registered successfully',
            201
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        try {
            $token = Auth::guard('api')->attempt($credentials);
        } catch (JWTException) {
            return $this->errorResponse('Could not create token', null, 500);
        }

        if (! $token) {
            return $this->errorResponse('Invalid credentials', null, 401);
        }

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'bearer',
            'user' => new UserResource(Auth::guard('api')->user()),
        ], 'Login successful');
    }
}
