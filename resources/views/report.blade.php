<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DevTrack Report — {{ $generatedAt }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-white text-gray-900 font-sans p-10 max-w-5xl mx-auto">

    {{-- Print button --}}
    <div class="no-print flex justify-end mb-6">
        <button onclick="window.print()"
                class="px-5 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
            Print / Save as PDF
        </button>
    </div>

    {{-- ================================================ --}}
    {{-- PAGE 1 — Summary --}}
    {{-- ================================================ --}}

    {{-- Report header --}}
    <div class="flex items-center justify-between border-b-2 border-green-700 pb-4 mb-6">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/PhilHealth_Logo.png') }}" alt="PhilHealth" class="h-14">
            <div>
                <h1 class="text-xl font-bold text-gray-800">DevTrack Device Status Report</h1>
                <p class="text-base text-gray-500">PhilHealth Office — Device Management System</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-400">Generated on</p>
            <p class="text-base font-medium text-gray-700">{{ $generatedAt }}</p>
            <p class="text-sm text-gray-400 mt-1">Period:
                {{ match($range) {
                    '1month'  => 'Last 1 month',
                    '3months' => 'Last 3 months',
                    '6months' => 'Last 6 months',
                    default   => 'All time'
                } }}
            </p>
        </div>
    </div>

    {{-- Summary cards --}}
    <h2 class="text-base font-semibold text-gray-600 uppercase tracking-wide mb-3">Device Summary</h2>
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div class="border border-gray-200 rounded-lg p-4">
            <p class="text-sm text-gray-400">Total devices</p>
            <p class="text-2xl font-bold text-gray-800">{{ $total }}</p>
        </div>
        @foreach ($statuses as $status)
            <div class="border border-gray-200 rounded-lg p-4">
                <p class="text-sm text-gray-400">{{ $status->label }}</p>
                <p class="text-2xl font-bold"
                   style="color: {{
                       match($status->color) {
                           'green'  => '#15803d',
                           'orange' => '#a16207',
                           'red'    => '#b91c1c',
                           default  => '#6b7280'
                       }
                   }};">
                    {{ $status->devices_count }}
                </p>
            </div>
        @endforeach
    </div>

    {{-- Status breakdown table --}}
    <h2 class="text-base font-semibold text-gray-600 uppercase tracking-wide mb-3">Status Breakdown</h2>
    <table class="w-full text-base border border-gray-200 rounded-lg overflow-hidden mb-8">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600 border-b border-gray-200">Status</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600 border-b border-gray-200">Count</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600 border-b border-gray-200">Percentage</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($statuses as $status)
                <tr>
                    <td class="px-4 py-3 text-gray-700">{{ $status->label }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $status->devices_count }}</td>
                    <td class="px-4 py-3 text-gray-700">
                        {{ $total > 0 ? round(($status->devices_count / $total) * 100, 1) : 0 }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Type breakdown table --}}
    <h2 class="text-base font-semibold text-gray-600 uppercase tracking-wide mb-3">Device Type Breakdown</h2>
    <table class="w-full text-base border border-gray-200 rounded-lg overflow-hidden mb-8">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600 border-b border-gray-200">Type</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600 border-b border-gray-200">Count</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600 border-b border-gray-200">Percentage</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($typeCounts as $type => $count)
                <tr>
                    <td class="px-4 py-3 text-gray-700 capitalize">{{ $type }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $count }}</td>
                    <td class="px-4 py-3 text-gray-700">
                        {{ $total > 0 ? round(($count / $total) * 100, 1) : 0 }}%
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ================================================ --}}
    {{-- PAGE 2 — Room Breakdown --}}
    {{-- ================================================ --}}
    <div class="page-break"></div>

    {{-- Page 2 header --}}
    <div class="flex items-center justify-between border-b-2 border-green-700 pb-4 mb-6">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/PhilHealth_Logo.png') }}" alt="PhilHealth" class="h-14">
            <div>
                <h1 class="text-xl font-bold text-gray-800">DevTrack Device Status Report</h1>
                <p class="text-base text-gray-500">Room Breakdown</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-base font-medium text-gray-700">{{ $generatedAt }}</p>
        </div>
    </div>

    <h2 class="text-base font-semibold text-gray-600 uppercase tracking-wide mb-3">Room Breakdown</h2>
    <table class="w-full text-base border border-gray-200 rounded-lg overflow-hidden mb-8">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600 border-b border-gray-200">Room</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600 border-b border-gray-200">Total</th>
                @foreach ($statuses as $status)
                    <th class="text-left px-4 py-3 font-medium text-gray-600 border-b border-gray-200">
                        {{ $status->label }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($rooms as $room)
                <tr>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $room->name }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $room->devices->count() }}</td>
                    @foreach ($statuses as $status)
                        <td class="px-4 py-3 text-gray-700">
                            {{ $room->devices->where('status_id', $status->id)->count() }}
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 2 + $statuses->count() }}"
                        class="px-4 py-8 text-center text-gray-400">
                        No rooms found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ================================================ --}}
    {{-- PAGE 3 — Maintenance Logs --}}
    {{-- ================================================ --}}
    <div class="page-break"></div>

    {{-- Page 3 header --}}
    <div class="flex items-center justify-between border-b-2 border-green-700 pb-4 mb-6">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/PhilHealth_Logo.png') }}" alt="PhilHealth" class="h-14">
            <div>
                <h1 class="text-xl font-bold text-gray-800">DevTrack Device Status Report</h1>
                <p class="text-base text-gray-500">Recent Maintenance Logs</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-base font-medium text-gray-700">{{ $generatedAt }}</p>
        </div>
    </div>

    <h2 class="text-base font-semibold text-gray-600 uppercase tracking-wide mb-3">
        Recent Maintenance Logs
        <span class="text-gray-400 font-normal normal-case">
            ({{ match($range) {
                '1month'  => 'Last 1 month',
                '3months' => 'Last 3 months',
                '6months' => 'Last 6 months',
                default   => 'All time'
            } }})
        </span>
    </h2>
    <table class="w-full text-base border border-gray-200 rounded-lg overflow-hidden mb-8">
        <thead class="bg-gray-50">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600 border-b border-gray-200">Date</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600 border-b border-gray-200">Device</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600 border-b border-gray-200">Room</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600 border-b border-gray-200">Performed by</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600 border-b border-gray-200">Before</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600 border-b border-gray-200">After</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600 border-b border-gray-200">Description</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($logs as $log)
                <tr>
                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($log->date)->format('M d, Y') }}
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $log->device->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $log->device->room?->name ?? 'Standalone' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $log->performedBy->username }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $log->statusBefore->label }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $log->statusAfter->label }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $log->description }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                        No maintenance logs found for this period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="border-t border-gray-200 pt-4 text-center text-sm text-gray-400">
        DevTrack — PhilHealth Office Device Management System — Generated {{ $generatedAt }}
    </div>

</body>
</html>
