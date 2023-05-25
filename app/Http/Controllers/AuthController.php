<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required',
            'password' => 'required|confirmed|min:8',
            'role' => 'required'
        ]);

        $role = $request->role;
        if (!in_array($role, ['customer', 'seller'])) {
            throw ValidationException::withMessages([
                'role' => 'Invalid role. Supported roles: customer, seller',
            ]);
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'profile_img' => null
        ]);

        $user->assignRole($role);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'role' => $role,
            'token' => $token,
        ], Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ])->status(Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'User authenticated successfully',
            'user' => $user,
            'role' => $user->getRoleNames(),
            'token' => $token,
        ], Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'User logged out successfully'], Response::HTTP_OK);
    }
}
