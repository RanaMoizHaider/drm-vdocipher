<?php

/**
 * File: VideoController.php
 * Description: Controller to handle video-related actions such as listing, uploading, and playing videos.
 * Author: Moiz Haider
 * Date: 11 October 2024
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VdoCipherService;

class VideoController extends Controller
{
    protected $vdoCipherService;

    // Constructor to initialize the VdoCipherService
    public function __construct(VdoCipherService $vdoCipherService)
    {
        $this->vdoCipherService = $vdoCipherService;
    }

    // Method to list all videos
    public function index()
    {
        $videos = $this->vdoCipherService->getVideos();
        return view('video.index', compact('videos'));
    }

    // Method to show the video upload form
    public function showUploadForm()
    {
        return view('video.upload');
    }

    // Method to handle video upload
    public function uploadVideo(Request $request)
    {
        // Validate the uploaded video file
        $request->validate([
            'video' => 'required|mimes:mp4|max:200000',
        ]);

        $file = $request->file('video');
        $filenameWithoutExtension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // Get the credentials for uploading
        $credentials = $this->vdoCipherService->getVideoCredentials($filenameWithoutExtension);

        if (!isset($credentials['clientPayload'])) {
            return redirect()->back()->withErrors('Failed to get upload credentials.');
        }

        $clientPayload = $credentials['clientPayload'];
        $uploadLink = $clientPayload['uploadLink'];
        unset($clientPayload['uploadLink']); // Remove 'uploadLink' from clientPayload

        $formData = array_merge($clientPayload, [
            'success_action_status' => 201,
            'success_action_redirect' => '',
        ]);

        try {
            // Upload the video to the API
            $response = $this->vdoCipherService->uploadVideoToApi($uploadLink, $formData, $file);

            if ($response->status() === 201) {
                return redirect()->route('video.index')->with('success', 'Video uploaded successfully.');
            } else {
                \Log::error('Video upload failed', ['response' => $response->body()]);
                return redirect()->back()->withErrors('Failed to upload video. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('Video upload exception', ['exception' => $e]);
            return redirect()->back()->withErrors('An error occurred while uploading the video.');
        }
    }

    // Method to play a video
    public function playVideo($videoID)
    {
        $cred = $this->vdoCipherService->getVideoOtp($videoID);
        return view('video.play', compact('cred'));
    }
}
