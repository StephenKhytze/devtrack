@extends('layouts.app')

@section('title', 'Add Device')

@section('toolbar')
    <a href="{{ route('devices.index') }}"
       class="flex items-center gap-2 text-base text-gray-500 hover:text-green-700 transition">
        ← Back to device list
    </a>
    <span class="text-base font-medium text-gray-800">Add new device</span>
@endsection

@section('content')
<div class="mt-4">

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-base">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('devices.store') }}"
          class="flex flex-col gap-4">
        @csrf

        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="grid grid-cols-2 gap-6">

                {{-- Column 1 --}}
                <div class="flex flex-col gap-4">

                    <div class="flex flex-col gap-1">
                        <label class="text-base font-medium text-gray-700">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-base font-medium text-gray-700">Type</label>
                        <select name="type"
                                class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                            @foreach (['desktop','printer','photocopier','telephone','aircon','appliance','network','monitor','other'] as $type)
                                <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-base font-medium text-gray-700">Model number</label>
                        <input type="text" name="model_num" value="{{ old('model_num') }}"
                               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-base font-medium text-gray-700">Status</label>
                        <select name="status_id"
                                class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
                                    {{ $status->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- Column 2 --}}
                <div class="flex flex-col gap-4">

                    <div class="flex flex-col gap-1">
                        <label class="text-base font-medium text-gray-700">Room</label>
                        <select name="room_id" id="room_id"
                                onchange="switchMap()"
                                class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                            <option value="">No room (standalone)</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}"
                                        data-image="{{ $room->image ? asset('images/rooms/' . $room->image) : '' }}"
                                        {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                    {{ $room->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1">
                            <label class="text-base font-medium text-gray-700">Position X (%)</label>
                            <input type="number" name="pos_x" id="pos_x" step="0.001"
                                   value="{{ old('pos_x') }}"
                                   oninput="syncCircleFromInputs()"
                                   class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-base font-medium text-gray-700">Position Y (%)</label>
                            <input type="number" name="pos_y" id="pos_y" step="0.001"
                                   value="{{ old('pos_y') }}"
                                   oninput="syncCircleFromInputs()"
                                   class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                        </div>
                    </div>

                    {{-- Map toggle --}}
                    <div>
                        <button type="button"
                                onclick="toggleMap()"
                                id="map-toggle-btn"
                                class="px-4 py-2 text-sm font-medium border border-gray-300 text-gray-600 rounded-md hover:bg-gray-100 transition">
                            Set position on map
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="hidden" name="sub_parts" value="0">
                        <input type="checkbox" name="sub_parts" id="sub_parts" value="1"
                               {{ old('sub_parts') ? 'checked' : '' }}
                               class="w-4 h-4 accent-green-700"
                               onchange="toggleParts()">
                        <label for="sub_parts" class="text-base font-medium text-gray-700">Has sub parts</label>
                    </div>

                    <div class="flex flex-col gap-1 flex-1">
                        <label class="text-base font-medium text-gray-700">Specs</label>
                        <textarea name="specs" rows="4"
                                  class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700 resize-none">{{ old('specs') }}</textarea>
                    </div>

                </div>

            </div>
        </div>

        {{-- Full width position map --}}
        <div id="position-map" class="hidden">
            <div class="bg-white border border-gray-200 rounded-xl px-4 pt-4 pb-2">
                <p class="text-base font-medium text-gray-700">Set position on map</p>
                <p class="text-sm text-gray-400 mt-1 mb-3">Click anywhere on the map to place the device. Change the room selection above to switch maps.</p>
            </div>

            <div id="map-container"
                 class="relative w-full border-2 border-green-700"
                 style="aspect-ratio: 1420 / 651;">

                <img id="map-image"
                     src="{{ asset('images/map.png') }}"
                     draggable="false"
                     class="absolute inset-0 w-full h-full object-contain select-none pointer-events-none"
                     alt="Map">

                <div id="map-placeholder"
                     class="hidden absolute inset-0 flex items-center justify-center text-base text-gray-400 pointer-events-none">
                    No room image available — enter position manually.
                </div>

                <div id="drag-circle"
                     class="absolute w-5 h-5 rounded-full bg-gray-500 border-2 border-white cursor-crosshair"
                     style="left: 50%; top: 50%; transform: translate(-50%, -50%); pointer-events: none;">
                </div>

            </div>
        </div>

        {{-- Parts section --}}
        <div id="parts-section"
             class="{{ old('sub_parts') ? '' : 'hidden' }} bg-white border border-gray-200 rounded-xl p-6 flex flex-col gap-4">

            <div class="flex items-center justify-between">
                <p class="text-base font-medium text-gray-700">Parts</p>
                <button type="button" onclick="addPartRow()"
                        class="px-3 py-1.5 text-sm font-medium border border-green-700 text-green-700 rounded-md hover:bg-green-50 transition">
                    + Add part
                </button>
            </div>

            <div id="parts-list" class="flex flex-col gap-3">
                @if (old('parts'))
                    @foreach (old('parts') as $i => $part)
                        <div class="part-row grid grid-cols-4 gap-3 pb-3 border-b border-gray-100">
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-500">Part name</label>
                                <input type="text" name="parts[{{ $i }}][name]"
                                       value="{{ $part['name'] ?? '' }}"
                                       class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-500">Model number</label>
                                <input type="text" name="parts[{{ $i }}][model_num]"
                                       value="{{ $part['model_num'] ?? '' }}"
                                       class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-500">Specs</label>
                                <input type="text" name="parts[{{ $i }}][specs]"
                                       value="{{ $part['specs'] ?? '' }}"
                                       class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-500">Status</label>
                                <select name="parts[{{ $i }}][status_id]"
                                        class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}"
                                            {{ ($part['status_id'] ?? '') == $status->id ? 'selected' : '' }}>
                                            {{ $status->label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-4">
                                <button type="button" onclick="removePartRow(this)"
                                        class="px-3 py-1.5 text-sm font-medium border border-red-300 text-red-600 rounded-md hover:bg-red-50 transition">
                                    Remove
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <p class="text-sm text-gray-400" id="no-parts-msg"
               style="{{ old('parts') ? 'display:none' : '' }}">
                No parts added yet. Click "+ Add part" to add one.
            </p>

        </div>

        {{-- Submit --}}
        <div class="flex gap-3 pb-6">
            <button type="submit"
                    class="px-5 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                Add device
            </button>
            <a href="{{ route('devices.index') }}"
               class="px-5 py-2 text-base font-medium border border-gray-300 text-gray-500 rounded-md hover:bg-gray-100 transition">
                Cancel
            </a>
        </div>

    </form>

</div>

<style>
    body.dragging { cursor: grabbing !important; user-select: none; }
</style>

<script>
    let isDragging    = false;
    let dragStartX    = 0;
    let dragStartY    = 0;
    let dragThreshold = 5;
    let partIndex     = {{ old('parts') ? count(old('parts')) : 0 }};
    const statuses    = @json($statuses);
    const floorMapUrl = "{{ asset('images/map.png') }}";

    // ── Map toggle ───────────────────────────────────────────

    function toggleMap() {
        const map      = document.getElementById('position-map');
        const btn      = document.getElementById('map-toggle-btn');
        const isHidden = map.classList.contains('hidden');
        map.classList.toggle('hidden', !isHidden);
        btn.textContent = isHidden ? 'Hide map' : 'Set position on map';
        if (isHidden) {
            syncCircleFromInputs();
            switchMap();
        }
    }

    function switchMap() {
        const select    = document.getElementById('room_id');
        const selected  = select.options[select.selectedIndex];
        const imageUrl  = selected ? selected.getAttribute('data-image') : '';

        const mapImage       = document.getElementById('map-image');
        const mapPlaceholder = document.getElementById('map-placeholder');
        const dragCircle     = document.getElementById('drag-circle');

        if (select.value !== '' && !imageUrl) {
            mapImage.classList.add('hidden');
            mapPlaceholder.classList.remove('hidden');
            dragCircle.classList.add('hidden');
        } else {
            mapImage.src = imageUrl || floorMapUrl;
            mapImage.classList.remove('hidden');
            mapPlaceholder.classList.add('hidden');
            dragCircle.classList.remove('hidden');
        }
    }

    // ── Drag logic ───────────────────────────────────────────

    document.addEventListener('DOMContentLoaded', () => {
        const mapContainer = document.getElementById('map-container');

        mapContainer.addEventListener('mousedown', e => {
            isDragging = true;
            dragStartX = e.clientX;
            dragStartY = e.clientY;
            e.preventDefault();

            const rect = mapContainer.getBoundingClientRect();
            const x    = Math.max(0, Math.min(100, ((e.clientX - rect.left) / rect.width)  * 100));
            const y    = Math.max(0, Math.min(100, ((e.clientY - rect.top)  / rect.height) * 100));

            document.getElementById('pos_x').value = x.toFixed(3);
            document.getElementById('pos_y').value = y.toFixed(3);
            moveCircle(x, y);
        });

        syncCircleFromInputs();
        switchMap();
    });

    document.addEventListener('mousemove', e => {
        if (!isDragging) return;

        const dx = Math.abs(e.clientX - dragStartX);
        const dy = Math.abs(e.clientY - dragStartY);
        if (dx < dragThreshold && dy < dragThreshold) return;

        document.body.classList.add('dragging');

        const placeholder = document.getElementById('map-placeholder');
        if (!placeholder.classList.contains('hidden')) return;

        const container = document.getElementById('map-container');
        const rect      = container.getBoundingClientRect();

        const x = Math.max(0, Math.min(100, ((e.clientX - rect.left) / rect.width)  * 100));
        const y = Math.max(0, Math.min(100, ((e.clientY - rect.top)  / rect.height) * 100));

        document.getElementById('pos_x').value = x.toFixed(3);
        document.getElementById('pos_y').value = y.toFixed(3);
        moveCircle(x, y);
    });

    document.addEventListener('mouseup', () => {
        isDragging = false;
        document.body.classList.remove('dragging');
    });

    function moveCircle(x, y) {
        const circle       = document.getElementById('drag-circle');
        circle.style.left      = x + '%';
        circle.style.top       = y + '%';
        circle.style.transform = 'translate(-50%, -50%)';
    }

    function syncCircleFromInputs() {
        const x = parseFloat(document.getElementById('pos_x').value) || 50;
        const y = parseFloat(document.getElementById('pos_y').value) || 50;
        moveCircle(x, y);
    }

    // ── Parts ────────────────────────────────────────────────

    function toggleParts() {
        const checked = document.getElementById('sub_parts').checked;
        document.getElementById('parts-section').classList.toggle('hidden', !checked);
    }

    function addPartRow() {
        document.getElementById('no-parts-msg').style.display = 'none';

        const statusOptions = statuses.map(s =>
            `<option value="${s.id}">${s.label}</option>`
        ).join('');

        const row = document.createElement('div');
        row.className = 'part-row grid grid-cols-4 gap-3 pb-3 border-b border-gray-100';
        row.innerHTML = `
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Part name</label>
                <input type="text" name="parts[${partIndex}][name]"
                       class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Model</label>
                <input type="text" name="parts[${partIndex}][model_num]"
                       class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-700">Serial number</label>
                <input type="text" name="serial_number"
                    value="{{ old('serial_number', $device->serial_number ?? '') }}"
                    class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Specs</label>
                <input type="text" name="parts[${partIndex}][specs]"
                       class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Status</label>
                <select name="parts[${partIndex}][status_id]"
                        class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                    ${statusOptions}
                </select>
            </div>
            <div class="col-span-4">
                <button type="button" onclick="removePartRow(this)"
                        class="px-3 py-1.5 text-sm font-medium border border-red-300 text-red-600 rounded-md hover:bg-red-50 transition">
                    Remove
                </button>
            </div>
        `;

        document.getElementById('parts-list').appendChild(row);
        partIndex++;
    }

    function removePartRow(btn) {
        btn.closest('.part-row').remove();
        if (document.querySelectorAll('.part-row').length === 0) {
            document.getElementById('no-parts-msg').style.display = '';
        }
    }

    // ── Init ─────────────────────────────────────────────────

    document.addEventListener('DOMContentLoaded', () => {
        syncCircleFromInputs();
        switchMap();
    });
</script>

@endsection
