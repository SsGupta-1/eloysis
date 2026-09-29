<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Api\LoginRequest;
use App\Services\Api\LoginService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function __construct(
        protected LoginService $loginService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $result = $this->loginService->login(
            $data['login'],
            $data['password'],
            $request->ip()
        );

        return $this->success(
            'Logged in successfully.',
            [
                'token' => $result['token'],
                'user' => $result['user'],
            ],
            200
        );
    }

    public function getProfile(Request $request): JsonResponse
    {
        return $this->success(
            'Profile fetched successfully.',
            [
                'user' => $request->user(),
            ],
            200
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->loginService->logout($request->user());

        return $this->success(
            'Logged out successfully.',
            [],
            200
        );
    }
}
