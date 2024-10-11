{{--
    File: index.blade.php
    Description: View to list all available videos with options to play them.
    Author: Moiz Haider
    Date: 11 October 2024
--}}

<x-layouts.app>

    <x-slot:title>All Videos</x-slot:title>

    <h1 class="text-3xl font-bold mb-6">Available Videos</h1>

    <!-- Display success message if video is uploaded successfully -->
    @if(session('success'))
        <div class="mx-auto mt-8">
            <p class="text-green-600 mb-4">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Check if there are videos -->
    @if(count($videos) > 0)
        <!-- List all videos -->
        <ul class="space-y-4">
            @foreach ($videos as $video)
                <li class="flex justify-between items-center p-4 bg-gray-800 rounded-lg shadow-md text-white">
                    <span>{{ $video['title'] }}</span>
                    @if($video['status'] == 'ready')
                        <!-- Link to play the video -->
                        <a href="{{ route('video.play', ['videoID' => $video['id']]) }}"
                           class="bg-blue-500 py-2 px-4 rounded-lg hover:bg-blue-600">
                            Play
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <!-- Message when no videos are available -->
        <p class="text-xl text-gray-500">No videos available.</p>
    @endif

</x-layouts.app>
