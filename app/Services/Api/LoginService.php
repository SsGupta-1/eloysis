<?php

namespace App\Services\Api;

use App\Models\User;
use App\Repositories\Api\LoginRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginService
{
    public function __construct(
        protected LoginRepository $login_repository
    ) {}

    public function login(string $login, string $password, ?string $ip = null): array
    {
        $user = $this->login_repository->find_for_login($login);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Invalid login credentials.'],
            ]);
        }

        $token = $user->createToken('student-mobile-app')->plainTextToken;

        $this->login_repository->update_login_details($user, $ip);

        return [
            'token' => $token,
            'user' => $this->format_user($user),
        ];
    }

    public function logout(User $user): bool
    {
        return (bool) $user->currentAccessToken()?->delete();
    }

    protected function format_user(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'profile_image' => $user->profile_image_url,
        ];
    }
}
