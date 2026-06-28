@extends('layouts.app')

@section('title', $room->name)

@section('toolbar')
    <a href="{{ route('floor.index') }}"
       class="flex items-center gap-2 text-base text-gray-500 hover:text-green-700 transition">
        ← Back to floor layout
    </a>
    <span class="text-base font-medium text-gray-800">{{ $room->name }}</span>
@endsection

@section('content')
<div class="mt-4 flex gap-6">

    {{-- Room image / layout --}}
    <div class="flex-1 border-2 border-green-700 rounded-xl overflow-hidden relative"
         style="aspect-ratio: 1420 / 651;">

        @if ($room->image)
            <img src="{{ asset('images/rooms/' . $room->image) }}"
                 class="absolute inset-0 w-full h-full object-contain"
                 alt="{{ $room->name }} layout">
        @else
            <div class="absolute inset-0 flex items-center justify-center text-gray-300 text-base">
                No room layout image available.
            </div>
        @endif

        {{-- Device circles --}}
        @foreach ($room->devices as $device)
            <div class="absolute flex flex-col items-center gap-1 cursor-pointer group"
                 style="left: {{ $device->pos_x }}%; top: {{ $device->pos_y }}%;"
                 onclick="openModal({{ $device->id }})">

                    <div class="w-5 h-5 rounded-full border-2 border-white transition group-hover:scale-125"
                        style="background-color: {{
                         match($device->status->color) {
                             'green'  => '#16a34a',
                             'orange' => '#facc15',
                             'red'    => '#ef4444',
                             default  => '#9ca3af'
                         }
                     }};"></div>

                <span class="absolute -top-6 left-1/2 -translate-x-1/2 bg-white text-gray-800
                             text-sm px-2 py-0.5 rounded shadow opacity-0 group-hover:opacity-100
                             transition whitespace-nowrap border border-gray-200">
                    {{ $device->name }}
                </span>
            </div>
        @endforeach

    </div>

    {{-- Device list panel --}}
    <div class="w-72 bg-white border border-gray-200 rounded-xl p-4 flex flex-col gap-4">
        <div>
            <h2 class="text-base font-semibold text-gray-800">{{ $room->name }} devices</h2>
            <p class="text-base text-gray-400">{{ $room->devices->count() }} device(s) total</p>
        </div>

        <input type="text"
               id="device-search"
               placeholder="Search..."
               oninput="filterDevices()"
               class="text-base border border-gray-300 rounded-md px-3 py-2 w-full focus:outline-none focus:ring-1 focus:ring-green-700">

        <div class="flex flex-col divide-y divide-gray-100" id="device-list">
            @forelse ($room->devices as $device)
                <div class="device-row py-2 flex justify-between items-center cursor-pointer hover:bg-gray-50 px-1 rounded"
                     onclick="openModal({{ $device->id }})">
                    <span class="text-base text-gray-800 device-name">{{ $device->name }}</span>
                    <span class="text-sm font-medium"
                          style="color: {{
                              match($device->status->color) {
                                  'green'  => '#15803d',
                                  'orange' => '#a16207',
                                  'red'    => '#b91c1c',
                                  default  => '#6b7280'
                              }
                          }};">
                        {{ $device->status->label }}
                    </span>
                </div>
            @empty
                <p class="text-base text-gray-400 py-2">No devices in this room.</p>
            @endforelse
        </div>

        <a href="{{ route('devices.index', ['room' => $room->id]) }}"
           class="mt-auto w-full text-center px-4 py-2 text-base font-medium border border-gray-300 rounded-md hover:bg-gray-100 transition">
            View full device list
        </a>
    </div>

</div>

{{-- Device modal --}}
<div id="device-modal"
     class="fixed inset-0 z-50 flex items-center justify-center hidden"
     onclick="closeModal(event)">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40"></div>

    {{-- Modal box --}}
    <div class="relative bg-white rounded-xl shadow-lg w-full max-w-lg mx-4 z-10 overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <h3 id="modal-name" class="text-base font-semibold text-gray-800"></h3>
                <p id="modal-type" class="text-sm text-gray-400 capitalize"></p>
            </div>
            <button onclick="closeModalDirect()"
                    class="text-gray-400 hover:text-gray-600 text-xl font-light leading-none">
                ✕
            </button>
        </div>

        {{-- Status badge --}}
        <div class="px-6 py-3 border-b border-gray-100 flex items-center gap-2">
            <span class="text-base text-gray-500">Status:</span>
            <span id="modal-status"
                  class="px-3 py-1 rounded-full text-sm font-medium"></span>
        </div>

        {{-- Details --}}
        <div class="px-6 py-4 flex flex-col gap-3">
            <div class="flex gap-2">
                <span class="text-base text-gray-400 w-28 shrink-0">Serial number</span>
                <span id="modal-serial" class="text-base text-gray-700"></span>
            </div>
            <div class="flex gap-2">
                <span class="text-base text-gray-400 w-28 shrink-0">Model</span>
                <span id="modal-model" class="text-base text-gray-700"></span>
            </div>
            <div class="flex gap-2">
                <span class="text-base text-gray-400 w-28 shrink-0">Specs</span>
                <span id="modal-specs" class="text-base text-gray-700"></span>
            </div>
            <div class="flex gap-2">
                <span class="text-base text-gray-400 w-28 shrink-0">Room</span>
                <span id="modal-room" class="text-base text-gray-700"></span>
            </div>
        </div>

        {{-- Parts --}}
        <div id="modal-parts-section" class="hidden px-6 pb-4">
            <p class="text-base font-medium text-gray-700 mb-2">Parts</p>
            <div id="modal-parts" class="flex flex-col gap-1"></div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2">
            <a id="modal-edit-link" href="#"
               class="px-4 py-2 text-base font-medium border border-gray-300 rounded-md hover:bg-gray-100 transition">
                Edit device
            </a>
            <button onclick="closeModalDirect()"
                    class="px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                Close
            </button>
        </div>

    </div>
</div>

{{-- Device data passed to JS --}}
<script>
    const devices = @json($room->devices->load('status', 'room', 'parts.status'));
    const editBaseUrl = "{{ url('/devices') }}";

    const statusColors = {
        green:  { bg: '#dcfce7', text: '#15803d' },
        orange: { bg: '#fef9c3', text: '#a16207' },
        red:    { bg: '#fee2e2', text: '#b91c1c' },
    };

    function openModal(deviceId) {
        const device = devices.find(d => d.id === deviceId);
        if (!device) return;

        document.getElementById('modal-name').textContent  = device.name;
        document.getElementById('modal-type').textContent  = device.type;
        document.getElementById('modal-serial').textContent = device.serial_number || '—';
        document.getElementById('modal-model').textContent = device.model_num || '—';
        document.getElementById('modal-specs').textContent = device.specs    || '—';
        document.getElementById('modal-room').textContent  = device.room ? device.room.name : 'Standalone';

        const color  = device.status?.color || 'default';
        const colors = statusColors[color] || { bg: '#f3f4f6', text: '#6b7280' };
        const badge  = document.getElementById('modal-status');
        badge.textContent         = device.status?.label || '—';
        badge.style.background    = colors.bg;
        badge.style.color         = colors.text;

        document.getElementById('modal-edit-link').href = `${editBaseUrl}/${device.id}/edit`;

        const partsSection = document.getElementById('modal-parts-section');
        const partsList    = document.getElementById('modal-parts');

        if (device.sub_parts && device.parts && device.parts.length > 0) {
            partsList.innerHTML = device.parts.map(part => {
                const pc = statusColors[part.status?.color] || { bg: '#f3f4f6', text: '#6b7280' };
                return `
                    <div class="flex justify-between items-center py-1.5 border-b border-gray-100 text-base">
                        <div>
                            <span class="text-gray-800">${part.name}</span>
                            ${part.model_num ? `<span class="text-gray-400 text-sm ml-2">${part.model_num}</span>` : ''}
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-sm font-medium"
                              style="background:${pc.bg}; color:${pc.text};">
                            ${part.status?.label || '—'}
                        </span>
                    </div>
                `;
            }).join('');
            partsSection.classList.remove('hidden');
        } else {
            partsSection.classList.add('hidden');
        }

        document.getElementById('device-modal').classList.remove('hidden');
    }

    function closeModal(event) {
        if (event.target === document.getElementById('device-modal')) {
            closeModalDirect();
        }
    }

    function closeModalDirect() {
        document.getElementById('device-modal').classList.add('hidden');
    }

    function filterDevices() {
        const search = document.getElementById('device-search').value.toLowerCase();
        document.querySelectorAll('.device-row').forEach(row => {
            const name = row.querySelector('.device-name').textContent.toLowerCase();
            row.style.display = name.includes(search) ? '' : 'none';
        });
    }
</script>
@endsection
