<?php

namespace App\Repositories\Api;

use App\Models\User;

class LoginRepository
{
    public function find_for_login(string $login): ?User
    {
        return User::query()
            ->where('role_id', 4)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->where(function ($query) use ($login) {
                $query->where('email', $login)
                    ->orWhere('username', $login)
                    ->orWhere('mobile', $login);
            })
            ->first();
    }

    public function update_login_details(User $student, ?string $ip = null): bool
    {
        return $student->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }
}