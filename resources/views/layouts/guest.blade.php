<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                background: linear-gradient(135deg, #F6F0D7 0%, #E8EFD9 50%, #D9E4C2 100%);
                min-height: 100vh;
            }
            .auth-card {
                backdrop-filter: blur(10px);
                background: rgba(255, 255, 255, 0.95);
            }
            .leaf-icon {
                animation: sway 3s ease-in-out infinite;
            }
            @keyframes sway {
                0%, 100% { transform: rotate(-3deg); }
                50% { transform: rotate(3deg); }
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <!-- Header with Back to Home Button -->
        <header class="fixed top-3 sm:top-4 md:top-6 left-0 right-0 z-50 flex justify-center px-3 sm:px-4 md:px-0">
            <div class="bg-white/90 backdrop-blur-sm rounded-full shadow-lg px-4 sm:px-6 md:px-8 py-2.5 sm:py-3 md:py-3.5 inline-flex border border-white/50">
                <nav class="flex items-center justify-center">
                    <a href="{{ route('welcome') }}" class="text-gray-700 text-xs sm:text-sm md:text-base font-medium hover:text-green-700 transition-colors duration-300 relative group inline-flex items-center gap-1.5 sm:gap-2 whitespace-nowrap">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span class="hidden min-[400px]:inline">Back to Home</span>
                        <span class="min-[400px]:hidden">Home</span>
                    </a>
                </nav>
            </div>
        </header>

        <div class="min-h-screen flex flex-col justify-center items-center pt-20 sm:pt-24 md:pt-28 pb-8 px-4 sm:px-6">
            <!-- Logo / Brand -->
            <div class="mb-6 sm:mb-8 text-center">
                <div class="inline-flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-green-700 shadow-lg mb-3 sm:mb-4 leaf-icon">
                    <svg class="w-8 h-8 sm:w-9 sm:h-9 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17,8C8,10,5,18.5,3,22c0,0,2.5-2,9-2c0,0-4-1-4-5C8,11.67,10.67,9.33,17,8z"/>
                        <path d="M20.5,2C20.5,2,14,3,12,8c-2,5,0,7,0,7s2-2,5-4C20,9,21,4,20.5,2z"/>
                    </svg>
                </div>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">
                    BenguetCrop<span class="text-green-700">Map</span>
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Agricultural Decision Support System</p>
            </div>

            <!-- Auth Card -->
            <div class="w-full max-w-md auth-card px-5 sm:px-8 py-6 sm:py-8 shadow-xl rounded-2xl border border-gray-100">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <p class="mt-6 text-xs text-gray-400">&copy; {{ date('Y') }} BenguetCropMap. All rights reserved.</p>
        </div>
    </body>
</html>
