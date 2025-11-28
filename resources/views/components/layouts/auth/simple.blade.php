<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>

    <body class="min-h-screen antialiased">

        {{-- Full-screen background image --}}
        <div
            class="min-h-screen flex items-center justify-center bg-cover bg-center bg-no-repeat relative"
            style="background-image: url('{{ asset('images/coffe_shop.jpg') }}');"
        >

            {{-- Dark overlay --}}
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

            {{-- Centered card --}}
            <div class="relative z-10 flex w-full max-w-sm flex-col  p-6 md:p-10 rounded-xl">

                {{-- BIG LOGO --}}
                <div class="flex flex-col items-center">
                    <img
                        src="{{ asset('images/logo.png') }}"
                        class="h-60 w-auto mb-4 drop-shadow-xl"
                        alt="Northern Island Logo"
                    >
                </div>

                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @fluxScripts
    </body>
</html>
