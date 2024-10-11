<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VideoController extends Controller
{
    public function index()
    {
        // Calling API to get videos
        $response = Http::acceptJson()
            ->withHeaders([
                'Authorization' => 'Apisecret ' . config('services.vdocipher.key'),
            ])
            ->get('https://dev.vdocipher.com/api/videos')
            ->json();
        $videos = $response['rows'];

        return view('video.index', compact('videos'));
    }

    private function getCredentials($title)
    {
        // Calling API to get video credentials
        $response = Http::asForm()
            ->withHeaders([
                'Authorization' => 'Apisecret ' . config('services.vdocipher.key'),
            ])
            ->put('https://dev.vdocipher.com/api/videos?title=' . $title);

        return $response->json();
    }

    public function playVideo($videoID)
    {
        $url = 'https://dev.vdocipher.com/api/videos/' . $videoID . '/otp';
        $response = Http::acceptJson()->contentType('application/json')
            ->withBody('{
              "ttl":300
            }')
            ->withHeaders([
                'Authorization' => 'Apisecret ' . config('services.vdocipher.key'),
            ])
            ->post($url);

        $cred = $response->json();

        return view('video.play', compact('cred'));
    }
}
