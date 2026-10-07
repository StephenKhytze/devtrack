@extends('layouts.app')

@section('title', 'Device Update Logs')

@section('toolbar')
    <a href="{{ route('devices.index') }}"
        class="flex items-center gap-2 text-base text-gray-500 hover:text-green-700 transition">
        ← Back to Device List
    </a>

    <span class="ml-auto text-base text-gray-500">
        Total records: <strong class="text-gray-800 font-medium">{{ $logs->total() }}</strong>
    </span>
@endsection

@section('content')
    <div class="mt-4 flex flex-col gap-4">

        {{-- Filter bar --}}
        <div class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col gap-3">
            <form method="GET" action="{{ route('devices.logs') }}" class="flex items-center gap-3 flex-wrap">

                {{-- Search --}}
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search logs..."
                    class="text-base border border-gray-300 rounded-md px-3 py-2 w-56 focus:outline-none focus:ring-1 focus:ring-green-700">

                {{-- Device filter --}}
                <select name="device_id"
                    class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-green-700">
                    <option value="">All devices</option>
                    @foreach ($devices as $d)
                        <option value="{{ $d->id }}" {{ request('device_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Action filter --}}
                <select name="action"
                    class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-green-700">
                    <option value="">All actions</option>
                    @foreach ($actions as $actKey => $actLabel)
                        <option value="{{ $actKey }}" {{ request('action') === $actKey ? 'selected' : '' }}>
                            {{ $actLabel }}
                        </option>
                    @endforeach
                </select>

                {{-- User filter --}}
                <select name="user_id"
                    class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-green-700">
                    <option value="">All staff</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->username }}
                        </option>
                    @endforeach
                </select>

                {{-- Date from --}}
                <input type="date" name="date_from" value="{{ request('date_from') }}" title="Date From"
                    class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-green-700">

                {{-- Date to --}}
                <input type="date" name="date_to" value="{{ request('date_to') }}" title="Date To"
                    class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-green-700">

                <button type="submit"
                    class="px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                    Filter
                </button>

                @if (request()->hasAny(['search', 'device_id', 'action', 'user_id', 'date_from', 'date_to']))
                    <a href="{{ route('devices.logs') }}"
                        class="px-4 py-2 text-base font-medium text-gray-500 border border-gray-300 rounded-md hover:bg-gray-100 transition">
                        Clear
                    </a>
                @endif
            </form>

            {{-- Sort Controls --}}
            <div class="flex items-center gap-2 pt-2 border-t border-gray-100 text-base">
                <span class="text-gray-500">Sort by:</span>
                <div class="flex border border-gray-300 rounded-md overflow-hidden">
                    @foreach ([
            'recent' => 'Recent',
            'device' => 'Device',
            'action' => 'Action',
        ] as $value => $label)
                        <a href="{{ request()->fullUrlWithQuery(['sort' => $value, 'page' => 1]) }}"
                            class="px-4 py-1.5 font-medium transition
                              {{ $sort === $value ? 'bg-green-700 text-white' : 'text-gray-500 hover:bg-gray-100' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Logs Table --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-base">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600 w-48">Date & Time</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600 w-52">Device</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600 w-40">Action</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Details / Description</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600 w-44">Performed By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-base text-gray-500 whitespace-nowrap">
                                {{ $log->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800">
                                @if ($log->device)
                                    <a href="{{ route('devices.edit', $log->device_id) }}"
                                        class="text-green-700 hover:text-green-900 hover:underline">
                                        {{ $log->device_name }}
                                    </a>
                                @else
                                    <span class="text-gray-700">{{ $log->device_name }}</span>
                                    <span class="text-xs text-gray-400 block">(deleted)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-base text-gray-800 whitespace-nowrap">
                                {{ $actions[$log->action] ?? ucfirst(str_replace('_', ' ', $log->action)) }}
                            </td>
                            <td class="px-4 py-3 text-base text-gray-700">
                                <p class="font-normal">{{ $log->description }}</p>
                                @if ($log->changes && is_array($log->changes) && count($log->changes) > 0)
                                    <div class="mt-1 flex flex-wrap gap-2 text-xs">
                                        @foreach ($log->changes as $field => $change)
                                            @if (is_array($change) && (isset($change['old']) || isset($change['new'])))
                                                <span
                                                    class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong>
                                                    <span
                                                        class="line-through text-gray-400">{{ $change['old'] ?: 'empty' }}</span>
                                                    <span>→</span>
                                                    <span
                                                        class="text-gray-900 font-medium">{{ $change['new'] ?: 'empty' }}</span>
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-base text-gray-600 whitespace-nowrap">
                                @if ($log->user)
                                    <span class="inline-flex items-center gap-1.5">
                                        <span>{{ $log->user->username }}</span>
                                        <span
                                            class="px-2 py-0.5 rounded-full text-sm {{ $log->user->access_type === 'admin' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $log->user->access_type }}
                                        </span>
                                    </span>
                                @else
                                    <span class="text-gray-400">System</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                                No update logs recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($logs->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
