<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

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
}
