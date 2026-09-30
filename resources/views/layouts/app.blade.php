<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'DevTrack')</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100">
<div class="content bg-gray-100 text-gray-900 font-sans mx-auto max-w-[100rem] flex flex-col gap-1">

    {{-- Header --}}
    <header class="w-full bg-white border-b border-gray-200 flex items-center px-6 py-4">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/Philhealth_Logo.png') }}" alt="PhilHealth logo" class="h-12">
            <h1 class="text-xl font-semibold">DevTrack</h1>
        </div>
        <div class="ml-auto flex items-center gap-4">
            <span class="text-base text-gray-500">
                {{ Auth::user()->username }}
                <span class="ml-1 px-2 py-0.5 text-sm rounded-full
                    {{ Auth::user()->access_type === 'admin' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ Auth::user()->access_type }}
                </span>
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="px-4 py-1.5 text-base font-medium border border-gray-300 text-gray-500 rounded-md hover:bg-gray-100 transition">
                    Log out
                </button>
            </form>
        </div>
    </header>

    {{-- Navbar --}}
    <div class="bg-white border-b border-gray-200 px-6 py-3 flex items-center gap-6 flex-wrap">
        <div class="flex border border-gray-300 rounded-md overflow-hidden">
            <a href="{{ route('dashboard') }}"
               class="px-5 py-2 text-base font-medium transition
                      {{ request()->routeIs('dashboard') ? 'bg-green-700 text-white' : 'text-gray-500 hover:bg-gray-100' }}">
                Dashboard
            </a>
            <a href="{{ route('floor.index') }}"
               class="px-5 py-2 text-base font-medium transition
                      {{ request()->routeIs('floor*') ? 'bg-green-700 text-white' : 'text-gray-500 hover:bg-gray-100' }}">
                Floor layout
            </a>
            <a href="{{ route('devices.index') }}"
               class="px-5 py-2 text-base font-medium transition
                      {{ request()->routeIs('devices.index') || (request()->routeIs('devices.*') && !request()->routeIs('devices.logs*')) ? 'bg-green-700 text-white' : 'text-gray-500 hover:bg-gray-100' }}">
                Full list
            </a>
            <a href="{{ route('devices.logs') }}"
               class="px-5 py-2 text-base font-medium transition
                      {{ request()->routeIs('devices.logs*') ? 'bg-green-700 text-white' : 'text-gray-500 hover:bg-gray-100' }}">
                Device logs
            </a>
            <a href="{{ route('maintenance.index') }}"
               class="px-5 py-2 text-base font-medium transition
                      {{ request()->routeIs('maintenance*') ? 'bg-green-700 text-white' : 'text-gray-500 hover:bg-gray-100' }}">
                Maintenance
            </a>
            <a href="{{ route('storage.index') }}"
               class="px-5 py-2 text-base font-medium transition
                      {{ request()->routeIs('storage*') ? 'bg-green-700 text-white' : 'text-gray-500 hover:bg-gray-100' }}">
                Storage
            </a>
        </div>

        {{-- Extra toolbar content per page --}}
        @yield('toolbar')
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div id="flash-success"
            class="mt-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-base flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()"
                    class="text-green-500 hover:text-green-700 ml-4">✕</button>
        </div>
        <script>
            setTimeout(() => {
                const el = document.getElementById('flash-success');
                if (el) el.remove();
            }, 4000);
        </script>
    @endif

    @if (session('error'))
        <div id="flash-error"
            class="mt-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-base flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()"
                    class="text-red-500 hover:text-red-700 ml-4">✕</button>
        </div>
        <script>
            setTimeout(() => {
                const el = document.getElementById('flash-error');
                if (el) el.remove();
            }, 4000);
        </script>
    @endif

    {{-- Page content --}}
    @yield('content')

    {{-- Footer --}}
    <footer class="mt-2 px-6 py-8 border-t border-gray-200 bg-white text-sm text-gray-400 flex items-center gap-2">
        <span>&copy;</span>
        <span>2026 PhilHealth - LHIO San Pablo City.</span>
    </footer>

</div>
</body>
</html>
