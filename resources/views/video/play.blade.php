<x-layouts.app>

    <x-slot:title>Play Video</x-slot:title>

    <div class="flex justify-center items-center">
        <div class="relative w-full md:w-2/3 lg:w-1/2">
            {{-- Video Here--}}
            <div style="padding-top:52.73%;position:relative;">
                <iframe
                    src="https://player.vdocipher.com/v2/?otp={{ $cred['otp'] }}&playbackInfo={{ $cred['playbackInfo'] }}"
                    style="border:0;max-width:100%;position:absolute;top:0;left:0;height:100%;width:100%;"
                    allowfullscreen="true"
                    allow="encrypted-media"
                >
                </iframe>
            </div>
        </div>
    </div>

</x-layouts.app>
