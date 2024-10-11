<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class VdoCipherService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.vdocipher.key');
    }

    public function getVideos()
    {
        // Call the API to get videos
        $response = Http::acceptJson()
            ->withHeaders([
                'Authorization' => 'Apisecret ' . $this->apiKey,
            ])
            ->get('https://dev.vdocipher.com/api/videos')
            ->json();

        return $response['rows'] ?? [];
    }

    public function getVideoCredentials($title)
    {
        // Call the API to get video credentials
        $response = Http::asForm()
            ->withHeaders([
                'Authorization' => 'Apisecret ' . $this->apiKey,
            ])
            ->put('https://dev.vdocipher.com/api/videos?title=' . $title);

        return $response->json();
    }

    public function uploadVideoToApi($uploadLink, $formData, $file)
    {
        // Upload video using the upload link and form data
        return Http::asMultipart()
            ->attach('file', fopen($file->getRealPath(), 'r'), $file->getClientOriginalName())
            ->post($uploadLink, $formData);
    }

    public function getVideoOtp($videoID)
    {
        $url = 'https://dev.vdocipher.com/api/videos/' . $videoID . '/otp';
        $response = Http::acceptJson()
            ->withHeaders([
                'Authorization' => 'Apisecret ' . $this->apiKey,
            ])
            ->post($url, ['ttl' => 300]);

        return $response->json();
    }
}
