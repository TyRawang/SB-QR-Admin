<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseService extends BaseSupabaseService
{

    public static function sanitizeSearch(string $input): string
    {
        // Strip characters that have special meaning in PostgREST filters
        return preg_replace('/[().,*\\\\]/', '', $input);
    }

    // ─── Generic query methods ───

    public function get(string $table, array $params = []): array
    {
        $response = Http::withHeaders(array_merge($this->headers(), [
            'Prefer' => 'return=representation',
        ]))->get($this->url("/rest/v1/{$table}"), $params);

        return $this->handleResponse($response, "GET {$table}");
    }

    public function getSingle(string $table, array $params = []): ?array
    {
        $response = Http::withHeaders(array_merge($this->headers(), [
            'Accept' => 'application/vnd.pgrst.object+json',
        ]))->get($this->url("/rest/v1/{$table}"), $params);

        if ($response->status() === 406) {
            return null;
        }

        return $this->handleResponse($response, "GET single {$table}");
    }

    public function count(string $table, array $filters = []): int
    {
        $response = Http::withHeaders(array_merge($this->headers(), [
            'Prefer' => 'count=exact',
        ]))->head($this->url("/rest/v1/{$table}"), $filters);

        $range = $response->header('Content-Range');
        if ($range && preg_match('/\/(\d+)$/', $range, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }

    public function insert(string $table, array $data): array
    {
        $response = Http::withHeaders(array_merge($this->headers(), [
            'Prefer' => 'return=representation',
            'Content-Type' => 'application/json',
        ]))->post($this->url("/rest/v1/{$table}"), $data);

        return $this->handleResponse($response, "INSERT {$table}");
    }

    public function update(string $table, array $filters, array $data): array
    {
        $query = http_build_query($filters);
        $response = Http::withHeaders(array_merge($this->headers(), [
            'Prefer' => 'return=representation',
            'Content-Type' => 'application/json',
        ]))->patch($this->url("/rest/v1/{$table}?{$query}"), $data);

        return $this->handleResponse($response, "UPDATE {$table}");
    }

    public function delete(string $table, array $filters): array
    {
        $query = http_build_query($filters);
        $response = Http::withHeaders(array_merge($this->headers(), [
            'Prefer' => 'return=representation',
        ]))->delete($this->url("/rest/v1/{$table}?{$query}"));

        return $this->handleResponse($response, "DELETE {$table}");
    }

    public function rpc(string $function, array $params = []): mixed
    {
        $response = Http::withHeaders(array_merge($this->headers(), [
            'Content-Type' => 'application/json',
        ]))->post($this->url("/rest/v1/rpc/{$function}"), $params);

        return $this->handleResponse($response, "RPC {$function}");
    }

    // ─── Table-specific helpers ───

    public function getProfiles(array $params = []): array
    {
        return $this->get('profiles', array_merge([
            'select' => '*',
            'order' => 'created_at.desc',
        ], $params));
    }

    public function getProfile(string $id): ?array
    {
        return $this->getSingle('profiles', [
            'select' => '*',
            'id' => "eq.{$id}",
        ]);
    }

    public function getBoxes(array $params = []): array
    {
        return $this->get('box', array_merge([
            'select' => '*, locations(id, name), profiles!box_user_id_fkey(id, email, display_name)',
            'order' => 'created_at.desc',
        ], $params));
    }

    public function getBox(string $id): ?array
    {
        return $this->getSingle('box', [
            'select' => '*, locations(id, name), profiles!box_user_id_fkey(id, email, display_name)',
            'id' => "eq.{$id}",
        ]);
    }

    public function getBoxItems(array $params = []): array
    {
        return $this->get('box_items', array_merge([
            'select' => '*',
            'order' => 'created_at.desc',
        ], $params));
    }

    public function getBoxImages(array $params = []): array
    {
        return $this->get('box_images', array_merge([
            'select' => '*',
            'order' => 'uploaded_at.desc',
        ], $params));
    }

    public function getLocations(array $params = []): array
    {
        return $this->get('locations', array_merge([
            'select' => '*',
            'order' => 'name.asc',
        ], $params));
    }

    public function getLocation(string $id): ?array
    {
        return $this->getSingle('locations', [
            'select' => '*',
            'id' => "eq.{$id}",
        ]);
    }

    public function getFeedback(array $params = []): array
    {
        return $this->get('feedback', array_merge([
            'select' => '*, profiles(id, email, display_name)',
            'order' => 'created_at.desc',
        ], $params));
    }

    // ─── Edge Functions ───

    public function callEdgeFunction(string $slug, array $data = []): mixed
    {
        $response = Http::withHeaders([
            'apikey' => $this->serviceRoleKey,
            'Authorization' => 'Bearer ' . $this->serviceRoleKey,
            'Content-Type' => 'application/json',
        ])->post($this->url("/functions/v1/{$slug}"), $data);

        return $this->handleResponse($response, "Edge Function {$slug}");
    }

    // ─── Connection check ───

    public function healthCheck(): bool
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(5)
                ->get($this->url('/rest/v1/'), ['limit' => 0]);

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
