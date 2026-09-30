<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">

    <div class="min-h-screen lg:h-screen lg:overflow-hidden lg:flex">

        {{-- Left: fixed image panel --}}
        <div class="hidden lg:flex lg:w-1/2 relative bg-bordeaux-dark overflow-hidden">
            <img
                src="{{ asset('images/auth-bg.jpg') }}"
                alt=""
                class="absolute inset-0 w-full h-full object-cover"
                onerror="this.style.display='none'">
            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/40 to-black/25"></div>

            <div class="relative z-10 flex flex-col justify-between p-10 w-full text-white">
                <a href="/" class="text-lg font-semibold tracking-tight text-white">
                    {{ config('app.name', 'Uni Club') }}
                </a>

                <div class="max-w-md">
                    <p class="text-2xl font-semibold leading-tight tracking-tight">
                        Where students become community.
                    </p>
                    <p class="mt-3 text-sm text-white/70 leading-relaxed">
                        Join hundreds of students across Arabic, English, and French teams.
                    </p>

                    <div class="mt-6 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/15 backdrop-blur border border-white/20 flex items-center justify-center text-[10px] font-semibold">
                            UC
                        </div>
                        <div class="text-xs text-white/70">
                            <p class="text-white font-medium">University Club</p>
                            <p>Est. 2024</p>
                        </div>
                    </div>
                </div>

                <p class="text-xs text-white/40">
                    © {{ date('Y') }} {{ config('app.name', 'Uni Club') }}
                </p>
            </div>
        </div>

        {{-- Right: scrollable form panel --}}
        <div class="flex-1 lg:h-screen lg:overflow-y-auto bg-white">
            <div class="min-h-full flex flex-col justify-center py-8 px-5 sm:px-8 lg:px-12">

                {{-- Mobile logo --}}
                <div class="lg:hidden mb-6 text-center">
                    <a href="/" class="text-lg font-semibold text-gray-900 tracking-tight">
                        {{ config('app.name', 'Uni Club') }}
                    </a>
                </div>

                <div class="w-full max-w-sm mx-auto">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-xs text-gray-500 text-center lg:max-w-sm lg:mx-auto">
                    <a href="/" class="hover:text-bordeaux">← Back to home</a>
                </p>
            </div>
        </div>

    </div>
</body>

</html>