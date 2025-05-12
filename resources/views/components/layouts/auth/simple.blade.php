<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white antialiased">
    <div class="bg-background flex min-h-screen items-center justify-center">
        <div class="w-full max-w-6xl mx-auto px-4">
            {{ $slot }}
        </div>
    </div>
    @fluxScripts
</body>

</html>
