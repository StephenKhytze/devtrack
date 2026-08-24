@extends('layouts.app')

@section('title', 'Maintenance Logs')

@section('toolbar')
    <div class="flex items-center gap-2 flex-nowrap shrink-0 w-263">

        <form method="GET" action="{{ route('maintenance.index') }}"
              class="flex items-center gap-2 flex-nowrap">

            <input type="text" name="search"
                   value="{{ request('search') }}"
                   placeholder="Search..."
                   class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700 w-24">

            <select name="device"
                    class="text-base border border-gray-300 rounded-md truncate w-42 px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-green-700">
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
           class="px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition shrink-0 ml-auto">
            + Add Log
        </a>

    </div>

    {{-- Sort controls --}}
    <div class="flex items-center gap-2 shrink-0">
        <span class="text-base text-gray-500">Sort:</span>
        <div class="flex border border-gray-300 rounded-md overflow-hidden">
            @foreach ([
                'recent' => 'Recent',
                'alpha'  => 'A–Z',
                'room'   => 'Room',
                'type'   => 'Type',
            ] as $value => $label)
                <a href="{{ request()->fullUrlWithQuery(['sort' => $value, 'page' => 1]) }}"
                class="px-3 py-2 text-base font-medium transition
                        {{ $sort === $value ? 'bg-green-700 text-white' : 'text-gray-500 hover:bg-gray-100' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
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
                    <tr class="hover:bg-gray-50 transition cursor-pointer"
                        onclick="openLogModal({{ $log->id }})">
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
                            <span class="px-2 py-1 rounded-full text-base font-medium"
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
                            <span class="px-2 py-1 rounded-full text-base font-medium"
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
                        <td class="px-4 py-3" onclick="event.stopPropagation()">
                            <a href="{{ route('maintenance.edit', $log->id) }}"
                            class="px-3 py-1 text-base font-medium border border-gray-300 rounded-md hover:bg-gray-100 transition">
                                Edit
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

        @if ($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }}
                    of {{ $logs->total() }} devices
                </p>
                <div class="flex items-center gap-1">
                    {{-- Previous --}}
                    @if ($logs->onFirstPage())
                        <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-md cursor-not-allowed">←</span>
                    @else
                        <a href="{{ $logs->previousPageUrl() }}"
                        class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-100 transition">←</a>
                    @endif

                    {{-- Page numbers --}}
                    @foreach ($logs->getUrlRange(1, $logs->lastPage()) as $page => $url)
                        @if ($page == $logs->currentPage())
                            <span class="px-3 py-1.5 text-sm font-medium bg-green-700 text-white border border-green-700 rounded-md">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                            class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-100 transition">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if ($logs->hasMorePages())
                        <a href="{{ $logs->nextPageUrl() }}"
                        class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-100 transition">→</a>
                    @else
                        <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-md cursor-not-allowed">→</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

{{-- Maintenance log modal --}}
<div id="log-modal"
     class="fixed inset-0 z-50 flex items-center justify-center hidden"
     onclick="closeLogModal(event)">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative bg-white rounded-xl shadow-lg w-full max-w-2xl mx-4 z-10 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <h3 id="log-modal-device" class="text-base font-semibold text-gray-800"></h3>
                <p id="log-modal-room" class="text-base text-gray-400"></p>
            </div>
            <button onclick="closeLogModalDirect()"
                    class="text-gray-400 hover:text-gray-600 text-xl font-light leading-none">✕</button>
        </div>

        {{-- Status badges --}}
        <div class="px-6 py-3 border-b border-gray-100 flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-base text-gray-500">Before:</span>
                <span id="log-modal-before" class="px-3 py-1 rounded-full text-sm font-medium"></span>
            </div>
            <span class="text-gray-300">→</span>
            <div class="flex items-center gap-2">
                <span class="text-base text-gray-500">After:</span>
                <span id="log-modal-after" class="px-3 py-1 rounded-full text-sm font-medium"></span>
            </div>
        </div>

        {{-- Details --}}
        <div class="px-6 py-4 grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-400">Date</span>
                <span id="log-modal-date" class="text-base text-gray-700"></span>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-400">Deadline</span>
                <span id="log-modal-deadline" class="text-base text-gray-700"></span>
            </div>
            <div class="flex flex-col gap-1">
                <span class="text-sm font-medium text-gray-400">Performed by</span>
                <span id="log-modal-performed" class="text-base text-gray-700"></span>
            </div>
        </div>

        {{-- Description --}}
        <div class="px-6 pb-4">
            <span class="text-sm font-medium text-gray-400">Description</span>
            <p id="log-modal-description" class="text-base text-gray-700 mt-1 leading-relaxed"></p>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2">
            <a id="log-modal-edit" href="#"
               class="px-4 py-2 text-base font-medium border border-gray-300 rounded-md hover:bg-gray-100 transition">
                Edit log
            </a>
            <button onclick="closeLogModalDirect()"
                    class="px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                Close
            </button>
        </div>

    </div>
</div>

@php
    $logsForJs = $logs->map(fn($l) => [
        'id'          => $l->id,
        'device'      => $l->device->name,
        'room'        => $l->device->room?->name ?? 'Standalone',
        'performed'   => $l->performedBy->username,
        'date'        => \Carbon\Carbon::parse($l->date)->format('M d, Y'),
        'deadline'    => $l->deadline
                            ? \Carbon\Carbon::parse($l->deadline)->format('M d, Y')
                            : null,
        'deadlinePast'=> $l->deadline
                            ? \Carbon\Carbon::parse($l->deadline)->isPast()
                            : false,
        'description' => $l->description,
        'before'      => ['label' => $l->statusBefore->label, 'color' => $l->statusBefore->color],
        'after'       => ['label' => $l->statusAfter->label,  'color' => $l->statusAfter->color],
    ]);
@endphp

<script>
    const logs = @json($logsForJs);

    const editBase = "{{ url('/maintenance') }}";

    const statusColors = {
        green:  { bg: '#dcfce7', text: '#15803d' },
        orange: { bg: '#fef9c3', text: '#a16207' },
        red:    { bg: '#fee2e2', text: '#b91c1c' },
    };

    function openLogModal(id) {
        const log = logs.find(l => l.id === id);
        if (!log) return;

        document.getElementById('log-modal-device').textContent    = log.device;
        document.getElementById('log-modal-room').textContent      = log.room;
        document.getElementById('log-modal-date').textContent      = log.date;
        document.getElementById('log-modal-performed').textContent = log.performed;
        document.getElementById('log-modal-description').textContent = log.description;
        document.getElementById('log-modal-edit').href             = `${editBase}/${log.id}/edit`;

        // Deadline
        const deadlineEl = document.getElementById('log-modal-deadline');
        if (log.deadline) {
            deadlineEl.textContent  = log.deadline;
            deadlineEl.style.color  = log.deadlinePast ? '#b91c1c' : '#374151';
            deadlineEl.style.fontWeight = log.deadlinePast ? '600' : '400';
        } else {
            deadlineEl.textContent = '—';
            deadlineEl.style.color = '#9ca3af';
        }

        // Status badges
        const beforeColors = statusColors[log.before.color] || { bg: '#f3f4f6', text: '#6b7280' };
        const afterColors  = statusColors[log.after.color]  || { bg: '#f3f4f6', text: '#6b7280' };

        const beforeBadge = document.getElementById('log-modal-before');
        beforeBadge.textContent      = log.before.label;
        beforeBadge.style.background = beforeColors.bg;
        beforeBadge.style.color      = beforeColors.text;

        const afterBadge = document.getElementById('log-modal-after');
        afterBadge.textContent      = log.after.label;
        afterBadge.style.background = afterColors.bg;
        afterBadge.style.color      = afterColors.text;

        document.getElementById('log-modal').classList.remove('hidden');
    }

    function closeLogModal(event) {
        if (event.target === document.getElementById('log-modal')) closeLogModalDirect();
    }

    function closeLogModalDirect() {
        document.getElementById('log-modal').classList.add('hidden');
    }
</script>

@endsection
