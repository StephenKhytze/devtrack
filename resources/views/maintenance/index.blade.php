@extends('layouts.app')

@section('title', 'Maintenance Logs')

@section('toolbar')
    <form method="GET" action="{{ route('maintenance.index') }}"
          class="flex items-center gap-2 flex-1">

        <input type="text" name="search"
               value="{{ request('search') }}"
               placeholder="Search..."
               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700 w-36">

        <select name="device"
                class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-green-700">
            <option value="">All devices</option>
            @foreach ($devices as $device)
                <option value="{{ $device->id }}" {{ request('device') == $device->id ? 'selected' : '' }}>
                    {{ $device->name }}
                </option>
            @endforeach
        </select>

        <select name="status_after"
                class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-green-700">
            <option value="">All outcomes</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->id }}" {{ request('status_after') == $status->id ? 'selected' : '' }}>
                    {{ $status->label }}
                </option>
            @endforeach
        </select>

        <input type="date" name="date_from" value="{{ request('date_from') }}"
               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">

        <input type="date" name="date_to" value="{{ request('date_to') }}"
               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">

        <button type="submit"
                class="px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
            Search
        </button>

        @if (request('search') || request('device') || request('status_after') || request('date_from') || request('date_to'))
            <a href="{{ route('maintenance.index') }}"
               class="px-4 py-2 text-base font-medium text-gray-500 border border-gray-300 rounded-md hover:bg-gray-100 transition">
                Clear
            </a>
        @endif

    </form>

    <a href="{{ route('maintenance.create') }}"
       class="px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition ml-auto shrink-0">
        + Add Log
    </a>
@endsection

@section('content')

    <div class="py-3 text-base text-gray-500">
        Showing <strong class="text-gray-800">{{ $logs->count() }}</strong> log(s)
    </div>

    <div class="mb-6 bg-white border border-gray-200 rounded-xl overflow-hidden">
        <table class="w-full text-base">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Date</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Device</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Room</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Performed by</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status before</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status after</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Description</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Deadline</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($logs as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($log->date)->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            {{ $log->device->name }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $log->device->room?->name ?? 'Standalone' }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $log->performedBy->username }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-sm font-medium"
                                  style="
                                      background-color: {{
                                          match($log->statusBefore->color) {
                                              'green'  => '#dcfce7',
                                              'orange' => '#fef9c3',
                                              'red'    => '#fee2e2',
                                              default  => '#f3f4f6'
                                          }
                                      }};
                                      color: {{
                                          match($log->statusBefore->color) {
                                              'green'  => '#15803d',
                                              'orange' => '#a16207',
                                              'red'    => '#b91c1c',
                                              default  => '#6b7280'
                                          }
                                      }};">
                                {{ $log->statusBefore->label }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-sm font-medium"
                                  style="
                                      background-color: {{
                                          match($log->statusAfter->color) {
                                              'green'  => '#dcfce7',
                                              'orange' => '#fef9c3',
                                              'red'    => '#fee2e2',
                                              default  => '#f3f4f6'
                                          }
                                      }};
                                      color: {{
                                          match($log->statusAfter->color) {
                                              'green'  => '#15803d',
                                              'orange' => '#a16207',
                                              'red'    => '#b91c1c',
                                              default  => '#6b7280'
                                          }
                                      }};">
                                {{ $log->statusAfter->label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs truncate">
                            {{ $log->description }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                            @if ($log->deadline)
                                <span class="{{ \Carbon\Carbon::parse($log->deadline)->isPast() ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                                    {{ \Carbon\Carbon::parse($log->deadline)->format('M d, Y') }}
                                </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('maintenance.show', $log->id) }}"
                               class="px-3 py-1 text-sm font-medium border border-gray-300 rounded-md hover:bg-gray-100 transition">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            No maintenance logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
