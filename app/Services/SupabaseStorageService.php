<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SupabaseStorageService extends BaseSupabaseService
{

    public function listBuckets(): array
    {
        $response = Http::withHeaders($this->headers())
            ->get($this->url('/storage/v1/bucket'));

        return $this->handleResponse($response, 'list buckets');
    }

    public function listFiles(string $bucket, string $path = '', array $options = []): array
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->url("/storage/v1/object/list/{$bucket}"), array_merge([
                'prefix' => $path,
                'limit' => 100,
                'offset' => 0,
                'sortBy' => ['column' => 'name', 'order' => 'asc'],
            ], $options));

        return $this->handleResponse($response, "list files {$bucket}/{$path}");
    }

    public function getSignedUrl(string $bucket, string $path, int $expiresIn = 3600): string
    {
        $response = Http::withHeaders($this->headers())
            ->post($this->url("/storage/v1/object/sign/{$bucket}/{$path}"), [
                'expiresIn' => $expiresIn,
            ]);

        $data = $this->handleResponse($response, "sign {$bucket}/{$path}");

        return $this->url('/storage/v1' . $data['signedURL']);
    }

    public function deleteFile(string $bucket, array $paths): array
    {
        $response = Http::withHeaders($this->headers())
            ->delete($this->url("/storage/v1/object/{$bucket}"), [
                'prefixes' => $paths,
            ]);

        return $this->handleResponse($response, "delete from {$bucket}");
    }

    public function deleteFiles(string $bucket, array $paths): array
    {
        return $this->deleteFile($bucket, $paths);
    }

    public function getPublicUrl(string $bucket, string $path): string
    {
        return $this->url("/storage/v1/object/public/{$bucket}/{$path}");
    }
}
