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
}
