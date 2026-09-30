@extends('layouts.app')

@section('title', 'Storage')

@section('toolbar')
    @if (auth()->user()->access_type === 'admin')
        <a href="{{ route('devices.create') }}"
            class="ml-auto px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
            + Add device
        </a>
    @endif
@endsection

@section('content')
    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse ($storageRooms as $room)
            @php
                $goodCount = $room->devices->filter(fn($d) => $d->status?->color === 'green')->count();
                $maintCount = $room->devices->filter(fn($d) => $d->status?->color === 'orange')->count();
                $oosCount = $room->devices->filter(fn($d) => $d->status?->color === 'red')->count();
            @endphp
            <div class="bg-white border border-gray-200 rounded-xl p-5 flex flex-col justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ $room->name }}
                    </h3>

                    {{-- Status Breakdown matching Floor layout format --}}
                    <div class="mt-4 flex items-center gap-5 flex-wrap">
                        <div class="flex items-center gap-2 text-base text-gray-500">
                            <span class="w-3 h-3 rounded-full bg-green-600 inline-block"></span>
                            Good: <strong class="text-gray-800">{{ $goodCount }}</strong>
                        </div>
                        <div class="flex items-center gap-2 text-base text-gray-500">
                            <span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span>
                            Maintenance: <strong class="text-gray-800">{{ $maintCount }}</strong>
                        </div>
                        <div class="flex items-center gap-2 text-base text-gray-500">
                            <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
                            Out of service: <strong class="text-gray-800">{{ $oosCount }}</strong>
                        </div>
                    </div>
                </div>

                {{-- Action button --}}
                <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('storage.show', $room->id) }}"
                        class="inline-flex items-center gap-2 text-base font-medium text-green-700 hover:text-green-800">
                        <span>View storage</span>
                        <span>→</span>
                    </a>
                    @if (auth()->user()->access_type === 'admin')
                        <a href="{{ route('devices.create', ['room_id' => $room->id]) }}"
                            class="text-sm text-gray-400 hover:text-gray-600">
                            + Add device
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-2 bg-white border border-gray-200 rounded-xl p-8 text-center text-gray-400">
                No storage rooms found.
            </div>
        @endforelse
    </div>
@endsection
