@extends('layouts.app')

@section('title', 'Dashboard')

@section('toolbar')
    {{-- Date range filter --}}
    <div class="flex border border-gray-300 rounded-md overflow-hidden">
        @foreach (['all' => 'All time', '6months' => '6 months', '3months' => '3 months', '1month' => '1 month'] as $value => $label)
            <a href="{{ route('dashboard', ['range' => $value]) }}"
               class="px-4 py-2 text-base font-medium transition
                      {{ $range === $value ? 'bg-green-700 text-white' : 'text-gray-500 hover:bg-gray-100' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if (auth()->user()->access_type === 'admin')
        <a href="{{ route('users.index') }}"
        class="ml-auto px-4 py-2 text-base font-medium border border-gray-300 text-gray-600 rounded-md hover:bg-gray-100 transition">
            Manage users
        </a>
    @endif

    <a href="{{ route('report', ['range' => $range]) }}"
       target="_blank"
       class="px-4 py-2 text-base font-medium border border-gray-300 text-gray-600 rounded-md hover:bg-gray-100 transition">
        Print report
    </a>
@endsection

@section('content')
<div class="mt-4 flex flex-col gap-6 pb-6">

    {{-- Summary cards --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-5 flex flex-col gap-1">
            <p class="text-sm font-medium text-gray-400">Total devices</p>
            <p class="text-3xl font-semibold text-gray-800">{{ $total }}</p>
        </div>
        @foreach ($statuses as $status)
            <div class="bg-white border border-gray-200 rounded-xl p-5 flex flex-col gap-1">
                <p class="text-sm font-medium text-gray-400">{{ $status->label }}</p>
                <p class="text-3xl font-semibold"
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

    {{-- Charts --}}
    <div class="grid grid-cols-2 gap-4">

        {{-- Status pie chart --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-base font-medium text-gray-700 mb-4">Device status distribution</p>
            <div class="flex justify-center">
                <canvas id="statusChart" style="max-height: 260px;"></canvas>
            </div>
        </div>

        {{-- Type pie chart --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <p class="text-base font-medium text-gray-700 mb-4">Device type distribution</p>
            <div class="flex justify-center">
                <canvas id="typeChart" style="max-height: 260px;"></canvas>
            </div>
        </div>

    </div>

    {{-- Room breakdown --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="text-base font-medium text-gray-700">Room breakdown</p>
        </div>
        <table class="w-full text-base">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Room</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Total</th>
                    @foreach ($statuses as $status)
                        <th class="text-left px-4 py-3 font-medium text-gray-600">{{ $status->label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($rooms as $room)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $room->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $room->devices->count() }}</td>
                        @foreach ($statuses as $status)
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-sm font-medium"
                                      style="
                                          background-color: {{
                                              match($status->color) {
                                                  'green'  => '#dcfce7',
                                                  'orange' => '#fef9c3',
                                                  'red'    => '#fee2e2',
                                                  default  => '#f3f4f6'
                                              }
                                          }};
                                          color: {{
                                              match($status->color) {
                                                  'green'  => '#15803d',
                                                  'orange' => '#a16207',
                                                  'red'    => '#b91c1c',
                                                  default  => '#6b7280'
                                              }
                                          }};">
                                    {{ $room->devices->where('status_id', $status->id)->count() }}
                                </span>
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
    </div>

    {{-- Recent maintenance logs --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <p class="text-base font-medium text-gray-700">Recent maintenance logs</p>
            <a href="{{ route('maintenance.index') }}"
               class="text-sm text-green-700 hover:underline">View all</a>
        </div>
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
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($logs as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($log->date)->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $log->device->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $log->device->room?->name ?? 'Standalone' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $log->performedBy->username }}</td>
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
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
    // Status pie chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: @json($statuses->pluck('label')),
            datasets: [{
                data: @json($statuses->pluck('devices_count')),
                backgroundColor: [
                    '#16a34a',
                    '#facc15',
                    '#ef4444',
                ],
                borderWidth: 1,
                borderColor: '#fff',
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Type pie chart
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'pie',
        data: {
            labels: @json($typeCounts->keys()->map(fn($k) => ucfirst($k))),
            datasets: [{
                data: @json($typeCounts->values()),
                backgroundColor: [
                    '#3b82f6',
                    '#8b5cf6',
                    '#f97316',
                    '#06b6d4',
                    '#ec4899',
                    '#84cc16',
                    '#14b8a6',
                    '#f59e0b',
                    '#6b7280',
                ],
                borderWidth: 1,
                borderColor: '#fff',
            }]
        },
        options: {
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endsection
