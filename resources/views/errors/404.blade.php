<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Page Not Found — DevTrack</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center flex flex-col items-center gap-4">
        <img src="{{ asset('images/PhilHealth_Logo.png') }}" alt="PhilHealth" class="h-16">
        <h1 class="text-4xl font-bold text-gray-800">404</h1>
        <p class="text-lg font-medium text-gray-600">Page Not Found</p>
        <p class="text-sm text-gray-400">The page you are looking for does not exist.</p>
        <a href="{{ route('dashboard') }}"
           class="mt-2 px-5 py-2 text-sm font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
            Back to dashboard
        </a>
    </div>
</body>
</html>
