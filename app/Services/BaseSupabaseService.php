<?php

namespace App\Services;

use App\Exceptions\SupabaseException;
use Illuminate\Http\Client\Response;

abstract class BaseSupabaseService
{
    protected string $url;
    protected string $serviceRoleKey;

    public function __construct()
    {
        $this->url = config('services.supabase.url');
        $this->serviceRoleKey = config('services.supabase.service_role_key');
    }

    protected function headers(): array
    {
        return [
            'apikey' => $this->serviceRoleKey,
            'Authorization' => 'Bearer ' . $this->serviceRoleKey,
        ];
    }

    protected function url(string $path): string
    {
        return rtrim($this->url, '/') . '/' . ltrim($path, '/');
    }

    protected function handleResponse(Response $response, string $context = ''): mixed
    {
        if ($response->failed()) {
            throw new SupabaseException(
                "Supabase API error ({$context}): " . $response->body(),
                $response->status(),
                ['context' => $context, 'body' => $response->json()]
            );
        }

        return $response->json();
    }
}
