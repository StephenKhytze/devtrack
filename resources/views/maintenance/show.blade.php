@extends('layouts.app')

@section('title', 'Maintenance Log')

@section('toolbar')
    <a href="{{ route('maintenance.index') }}"
       class="flex items-center gap-2 text-base text-gray-500 hover:text-green-700 transition">
        ← Back to maintenance logs
    </a>
    <span class="text-base font-medium text-gray-800">Log #{{ $log->id }}</span>
@endsection

@section('content')
<div class="mt-4">
    <div class="bg-white border border-gray-200 rounded-xl p-6 flex flex-col gap-6">

        {{-- Header --}}
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">{{ $log->device->name }}</h2>
                <p class="text-base text-gray-400">
                    {{ $log->device->room?->name ?? 'Standalone' }} —
                    {{ \Carbon\Carbon::parse($log->date)->format('F d, Y') }}
                </p>
            </div>
            <a href="{{ route('maintenance.create.device', $log->device->id) }}"
               class="px-4 py-2 text-base font-medium border border-green-700 text-green-700 rounded-md hover:bg-green-50 transition">
                + Log new maintenance for this device
            </a>
        </div>

        <div class="grid grid-cols-2 gap-6">

            {{-- Left --}}
            <div class="flex flex-col gap-4">

                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">Performed by</p>
                    <p class="text-base text-gray-800">{{ $log->performedBy->username }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">Status before</p>
                    <span class="px-3 py-1 rounded-full text-sm font-medium"
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
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">Status after</p>
                    <span class="px-3 py-1 rounded-full text-sm font-medium"
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
                </div>

            </div>

            {{-- Right --}}
            <div>
                <p class="text-sm font-medium text-gray-400 mb-1">Description</p>
                <p class="text-base text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $log->description }}</p>
            </div>

        </div>

    </div>
</div>
@endsection
