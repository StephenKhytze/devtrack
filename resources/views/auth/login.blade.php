<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DevTrack — Login</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-sm">

        {{-- Logo and title --}}
        <div class="flex flex-col items-center gap-3 mb-8">
            <img src="{{ asset('images/PhilHealth_Logo.png') }}"
                 alt="PhilHealth logo"
                 class="h-16">
            <div class="text-center">
                <h1 class="text-2xl font-semibold text-gray-800">DevTrack</h1>
                <p class="text-base text-gray-400">PhilHealth Device Management</p>
            </div>
        </div>

        {{-- Login card --}}
        <div class="bg-white border border-gray-200 rounded-xl p-8 flex flex-col gap-5">

            @if ($errors->any())
                <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-base">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}"
                autocomplete="off"
                  class="flex flex-col gap-4">
                @csrf

                <div class="flex flex-col gap-1">
                    <label class="text-base font-medium text-gray-700">Username</label>
                    <input type="text" name="username"
                           autofocus
                           autocomplete="off"
                           class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-base font-medium text-gray-700">Password</label>
                    <input type="password" name="password"
                           class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember"
                           class="w-4 h-4 accent-green-700">
                    <label for="remember" class="text-base text-gray-600">Remember me</label>
                </div>

                <button type="submit"
                        class="w-full px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                    Log in
                </button>

            </form>

        </div>

    </div>

</body>
</html>
