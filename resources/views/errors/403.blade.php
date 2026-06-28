<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Access Denied — DevTrack</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center flex flex-col items-center gap-4">
        <img src="{{ asset('images/Philhealth_Logo.png') }}" alt="PhilHealth" class="h-16">
        <h1 class="text-4xl font-bold text-gray-800">403</h1>
        <p class="text-lg font-medium text-gray-600">Access Denied</p>
        <p class="text-base text-gray-400">You don't have permission to access this page.</p>
        <a href="{{ route('dashboard') }}"
           class="mt-2 px-5 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
            Back to dashboard
        </a>
    </div>
</body>
</html>
