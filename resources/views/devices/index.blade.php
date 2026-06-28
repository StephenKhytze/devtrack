@extends('layouts.app')

@section('title', 'Device List')

@section('toolbar')
    <form method="GET" action="{{ route('devices.index') }}"
          class="flex items-center gap-3 flex-wrap">

        {{-- Search --}}
        <input type="text" name="search"
               value="{{ request('search') }}"
               placeholder="Search devices..."
               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">

        {{-- Room filter --}}
        <select name="room"
                class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-green-700">
            <option value="">All rooms</option>
            @foreach ($rooms as $room)
                <option value="{{ $room->id }}" {{ request('room') == $room->id ? 'selected' : '' }}>
                    {{ $room->name }}
                </option>
            @endforeach
            <option value="standalone" {{ request('room') === 'standalone' ? 'selected' : '' }}>
                No room (standalone)
            </option>
        </select>

        {{-- Status filter --}}
        <select name="status"
                class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-green-700">
            <option value="">All statuses</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                    {{ $status->label }}
                </option>
            @endforeach
        </select>

        <button type="submit"
                class="px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
            Search
        </button>

        @if (request('search') || request('room') || request('status'))
            <a href="{{ route('devices.index') }}"
               class="px-4 py-2 text-base font-medium text-gray-500 border border-gray-300 rounded-md hover:bg-gray-100 transition">
                Clear
            </a>
        @endif

    </form>

    @if (auth()->user()->access_type === 'admin')
        <a href="{{ route('devices.create') }}"
           class="px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition ml-auto">
            + Add device
        </a>
    @endif
@endsection

@section('content')
    <div class="py-3 text-base text-gray-500">
        Showing <strong class="text-gray-800">{{ $devices->count() }}</strong> device(s)
    </div>

    <div class="mb-6 bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full text-base">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-3 py-3 font-medium text-gray-600">Name</th>
                    <th class="text-left px-3 py-3 font-medium text-gray-600">Type</th>
                    <th class="text-left px-3 py-3 font-medium text-gray-600">Serial number</th>
                    <th class="text-left px-3 py-3 font-medium text-gray-600">Model</th>
                    <th class="text-left px-3 py-3 font-medium text-gray-600 w-32">Specs</th>
                    <th class="text-left px-3 py-3 font-medium text-gray-600">Room</th>
                    <th class="text-left px-3 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-left px-3 py-3 font-medium text-gray-600">Parts</th>
                    <th class="text-left px-3 py-3 font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($devices as $device)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 py-3 font-medium text-gray-800 whitespace-nowrap">{{ $device->name }}</td>
                        <td class="px-3 py-3 capitalize text-gray-600 whitespace-nowrap">{{ $device->type }}</td>
                        <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $device->serial_number ?? '—' }}</td>
                        <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $device->model_num ?? '—' }}</td>
                        <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $device->specs ?? '—' }}</td>
                        <td class="px-3 py-3 text-gray-600 w-54">{{ $device->room?->name ?? 'Standalone' }}</td>
                        <td class="px-3 py-3">
                            <span class="px-2 py-1 rounded-full text-sm font-medium"
                                style="
                                    background-color: {{
                                        match($device->status->color) {
                                            'green'  => '#dcfce7',
                                            'orange' => '#fef9c3',
                                            'red'    => '#fee2e2',
                                            default  => '#f3f4f6'
                                        }
                                    }};
                                    color: {{
                                        match($device->status->color) {
                                            'green'  => '#15803d',
                                            'orange' => '#a16207',
                                            'red'    => '#b91c1c',
                                            default  => '#6b7280'
                                        }
                                    }};">
                                {{ $device->status->label }}
                            </span>
                        </td>
                        <td class="px-3 py-3">
                            @if ($device->sub_parts)
                                <button onclick="toggleParts({{ $device->id }})"
                                        class="text-sm text-green-700 underline hover:text-green-900 whitespace-nowrap">
                                    Show parts ({{ $device->parts->count() }})
                                </button>
                            @else
                                <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>
                        @if (Auth::user()->access_type === 'admin')
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('devices.edit', $device->id) }}"
                                    class="px-3 py-1 text-sm font-medium text-green-700 border border-green-700 rounded-md hover:bg-green-50 transition whitespace-nowrap">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('devices.destroy', $device->id) }}"
                                        onsubmit="return confirm('Delete {{ $device->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-1 text-sm font-medium text-red-600 border border-red-300 rounded-md hover:bg-red-50 transition whitespace-nowrap">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @else
                            <td class="px-3 py-3 text-gray-400 text-sm">—</td>
                        @endif
                    </tr>

                    @if ($device->sub_parts && $device->parts->isNotEmpty())
                        <tr id="parts-{{ $device->id }}" class="hidden bg-gray-50">
                            <td colspan="9" class="px-8 py-3">
                                <p class="text-sm font-medium text-gray-500 mb-2">Parts for {{ $device->name }}</p>
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-gray-500">
                                            <th class="text-left py-1 pr-4">Part name</th>
                                            <th class="text-left py-1 pr-4">Model</th>
                                            <th class="text-left py-1 pr-4">Specs</th>
                                            <th class="text-left py-1">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($device->parts as $part)
                                            <tr>
                                                <td class="py-2 pr-4 text-gray-700">{{ $part->name }}</td>
                                                <td class="py-2 pr-4 text-gray-500">{{ $part->model_num ?? '—' }}</td>
                                                <td class="py-2 pr-4 text-gray-500">{{ $part->specs ?? '—' }}</td>
                                                <td class="py-2">
                                                    <span class="px-2 py-0.5 rounded-full text-sm font-medium"
                                                        style="
                                                            background-color: {{
                                                                match($part->status->color) {
                                                                    'green'  => '#dcfce7',
                                                                    'orange' => '#fef9c3',
                                                                    'red'    => '#fee2e2',
                                                                    default  => '#f3f4f6'
                                                                }
                                                            }};
                                                            color: {{
                                                                match($part->status->color) {
                                                                    'green'  => '#15803d',
                                                                    'orange' => '#a16207',
                                                                    'red'    => '#b91c1c',
                                                                    default  => '#6b7280'
                                                                }
                                                            }};">
                                                        {{ $part->status->label }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endif

                @empty
                    <tr>
                        <td colspan="9" class="px-3 py-8 text-center text-gray-400">
                            No devices found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        function toggleParts(deviceId) {
            const row = document.getElementById('parts-' + deviceId);
            row.classList.toggle('hidden');
        }
    </script>
@endsection
