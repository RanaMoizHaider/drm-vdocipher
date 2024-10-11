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

    public function showUploadForm()
    {
        return view('video.upload');
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

    public function uploadVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:mp4|max:200000',
        ]);

        $file = $request->file('video');
        $filename = $file->getClientOriginalName();
        $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

        // Get the credentials for uploading
        $credentials = $this->getCredentials($filenameWithoutExtension);

        if (!isset($credentials['clientPayload'])) {
            return redirect()->back()->withErrors('Failed to get upload credentials.');
        }

        $clientPayload = $credentials['clientPayload'];
        $uploadLink = $clientPayload['uploadLink'];

        // Prepare the form data
        $formData = array_merge($clientPayload, [
            'success_action_status' => 201,
            'success_action_redirect' => '',
        ]);

        try {
            $response = Http::asMultipart()
                ->attach(
                    'file',
                    fopen($file->getRealPath(), 'r'),
                    $file->getClientOriginalName()
                )
                ->post($uploadLink, $formData);

            dd($response->json());

//            if ($response->status() === 201) {
//                return redirect()->route('video.index')->with('success', 'Video uploaded successfully.');
//            } else {
//                \Log::error('Video upload failed', ['response' => $response->body()]);
//                return redirect()->back()->withErrors('Failed to upload video. Please try again.');
//            }
        } catch (\Exception $e) {
            \Log::error('Video upload exception', ['exception' => $e]);
            return redirect()->back()->withErrors('An error occurred while uploading the video.');
        }
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
