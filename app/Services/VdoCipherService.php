<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class VdoCipherService
{
    protected $apiKey;
    protected $baseURL;

    // Constructor to initialize the API key from the configuration
    public function __construct()
    {
        $this->apiKey = config('services.vdocipher.key');
        $this->baseURL = config('services.vdocipher.base_url');
    }

    // Method to get the list of videos from the VdoCipher API
    public function getVideos()
    {
        // Call the API to get videos
        $response = Http::acceptJson()
            ->withHeaders([
                'Authorization' => 'Apisecret ' . $this->apiKey,
            ])
            ->get($this->baseURL)
            ->json();

        // Return the list of videos or an empty array if not found
        return $response['rows'] ?? [];
    }

    // Method to get video upload credentials from the VdoCipher API
    public function getVideoCredentials($title)
    {
        // Call the API to get video credentials
        $response = Http::asForm()
            ->withHeaders([
                'Authorization' => 'Apisecret ' . $this->apiKey,
            ])
            ->put($this->baseURL . '?title=' . $title);

        // Return the response as JSON
        return $response->json();
    }

    // Method to upload a video to the VdoCipher API
    public function uploadVideoToApi($uploadLink, $formData, $file)
    {
        // Upload video using the upload link and form data
        return Http::asMultipart()
            ->attach('file', fopen($file->getRealPath(), 'r'), $file->getClientOriginalName())
            ->post($uploadLink, $formData);
    }

    // Method to get a video OTP (One-Time Password) from the VdoCipher API
    public function getVideoOtp($videoID)
    {
        $url = $this->baseURL . '/' . $videoID . '/otp';
        $response = Http::acceptJson()
            ->withHeaders([
                'Authorization' => 'Apisecret ' . $this->apiKey,
            ])
            ->post($url, ['ttl' => 300]);

        // Return the response as JSON
        return $response->json();
    }
}
