<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $result = $this->service->login($credentials);

        if (!$result) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email or password associated with your role.'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'data' => $result
        ]);
    }

    public function logout(Request $request)
    {
        $this->service->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out and session terminated.'
        ]);
    }

    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid password.'
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Password verified successfully.'
        ]);
    }
}
