<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                background-color: #f5f5dc; /* Light beige matching landing page */
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <!-- Header with Back to Home Button -->
        <header class="fixed top-2 sm:top-3 md:top-6 left-0 right-0 z-50 flex justify-center px-2 sm:px-3 md:px-0">
            <div class="bg-white rounded-full shadow-xl px-3 sm:px-4 md:px-8 py-2 sm:py-3 md:py-4 inline-flex max-w-[calc(100%-16px)]">
                <nav class="flex items-center justify-center">
                    <a href="{{ route('welcome') }}" class="text-gray-900 text-xs sm:text-sm md:text-base font-medium hover:text-lime-400 transition-colors duration-300 relative group inline-flex items-center gap-1.5 sm:gap-2 whitespace-nowrap">
                        <span>←</span>
                        <span class="hidden min-[400px]:inline">Back to Home</span>
                        <span class="min-[400px]:hidden">Home</span>
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-lime-400 group-hover:w-full transition-all duration-300"></span>
                    </a>
                </nav>
            </div>
        </header>

        <div class="min-h-screen flex flex-col justify-center items-center pt-16 sm:pt-20 md:pt-24 pb-6 px-3 sm:px-4">
            <div class="mb-3 sm:mb-4 md:mb-6">
                <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 text-center">
                    BenguetCropMap
                </h1>
            </div>

            <div class="w-full max-w-md px-4 sm:px-6 py-4 sm:py-5 md:py-6 bg-white shadow-xl overflow-hidden rounded-2xl border-t-4 border-lime-400">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
