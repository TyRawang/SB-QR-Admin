<?php

namespace App\Services;

use App\Exceptions\SupabaseException;
use Illuminate\Support\Facades\Http;

class SupabaseAuthService extends BaseSupabaseService
{

    public function listUsers(int $page = 1, int $perPage = 50): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->url('/auth/v1/admin/users'), [
                'page' => $page,
                'per_page' => $perPage,
            ]);

        return $this->handleResponse($response, 'list users');
    }

    public function getUser(string $id): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->url("/auth/v1/admin/users/{$id}"));

        return $this->handleResponse($response, "get user {$id}");
    }

    public function updateUser(string $id, array $data): array
    {
        $response = Http::withHeaders($this->headers())
            ->put($this->url("/auth/v1/admin/users/{$id}"), $data);

        return $this->handleResponse($response, "update user {$id}");
    }

    public function deleteUser(string $id): array
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->url("/auth/v1/admin/users/{$id}"));

        return $this->handleResponse($response, "delete user {$id}");
    }

    public function sendPasswordReset(string $email): void
    {
        $response = Http::withHeaders(array_merge($this->headers(), [
            'Content-Type' => 'application/json',
        ]))->post($this->url('/auth/v1/recover'), [
            'email' => $email,
        ]);

        if ($response->failed()) {
            throw new SupabaseException(
                "Password reset failed for {$email}: " . $response->body(),
                $response->status()
            );
        }
    }
}
