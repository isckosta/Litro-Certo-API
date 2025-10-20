<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthService
{
    /**
     * Register a new user.
     */
    public static function register(array $data): array
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'is_active' => true,
            ]);

            $user->assignRole('user');

            DB::commit();

            return [
                'success' => true,
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Authenticate user and return JWT token.
     */
    public static function login(array $credentials): array
    {
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return [
                    'success' => false,
                    'message' => 'Invalid credentials',
                ];
            }

            return [
                'success' => true,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'user' => JWTAuth::user(),
            ];
        } catch (JWTException $e) {
            return [
                'success' => false,
                'message' => 'Could not create token',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Refresh JWT token.
     */
    public static function refresh(): array
    {
        try {
            $token = JWTAuth::refresh();

            return [
                'success' => true,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'user' => JWTAuth::user(),
            ];
        } catch (JWTException $e) {
            return [
                'success' => false,
                'message' => 'Could not refresh token',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Logout user and invalidate token.
     */
    public static function logout(): array
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return [
                'success' => true,
                'message' => 'Successfully logged out',
            ];
        } catch (JWTException $e) {
            return [
                'success' => false,
                'message' => 'Could not logout',
                'error' => $e->getMessage(),
            ];
        }
    }
}
