<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FaceService
{
    // Expect env('FACE_SERVICE_URL') to be set to a microservice endpoint
    public static function getEmbeddingFromImage(string $imagePath): ?array
    {
        $service = env('FACE_SERVICE_URL');
        if (!$service) {
            return null;
        }

        // Send multipart/form-data with file
        try {
            $response = Http::attach(
                'file', file_get_contents(storage_path('app/public/' . $imagePath)), basename($imagePath)
            )->post(rtrim($service, '/') . '/embedding');

            if ($response->successful()) {
                $data = $response->json();
                return $data['embedding'] ?? null;
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }
}
