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
                            @foreach (['desktop','laptop','printer','photocopier','telephone','aircon','appliance','network','monitor','other'] as $type)
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
                        <label class="text-base font-medium text-gray-700">Serial number</label>
                        <input type="text" name="serial_number" value="{{ old('serial_number') }}"
                               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-base font-medium text-gray-700">Inventory number</label>
                        <input type="text" name="inventory_number" value="{{ old('inventory_number') }}"
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
                                class="px-4 py-2 text-xs font-medium border border-gray-300 text-gray-600 rounded-md hover:bg-gray-100 transition">
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
        <div id="position-map" class="hidden flex flex-col gap-2">
            <div class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col gap-3">

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Set position on map</p>
                        <p class="text-xs text-gray-400">Click anywhere on the map to place the device.</p>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <span class="w-3 h-3 rounded-full bg-gray-500 inline-block border border-white"></span>
                        Device position
                    </div>
                </div>

                {{-- Map container — exact same rendering as floor layout --}}
                <div id="map-outer" class="relative w-full overflow-hidden"
                    style="aspect-ratio: 2307 / 1559;">

                    <img id="map-image"
                        src="{{ asset('images/map.png') }}"
                        draggable="false"
                        class="absolute inset-0 w-full h-full select-none pointer-events-none"
                        style="object-fit: fill;"
                        alt="Map">

                    <div id="map-placeholder"
                        class="hidden absolute inset-0 flex items-center justify-center text-sm text-gray-400 pointer-events-none bg-gray-50">
                        No room image available — enter position manually.
                    </div>

                    <div id="drag-circle"
                        class="absolute w-5 h-5 rounded-full bg-gray-500 border-2 border-white cursor-crosshair z-10"
                        style="left: 50%; top: 50%; transform: translate(-50%, -50%); pointer-events: none;">
                    </div>

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
                        <div class="part-row grid grid-cols-3 gap-3 pb-3 border-b border-gray-100">
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-500">Part name</label>
                                <input type="text" name="parts[{{ $i }}][name]"
                                       value="{{ $part['name'] ?? '' }}"
                                       class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-500">Model</label>
                                <input type="text" name="parts[{{ $i }}][model_num]"
                                       value="{{ $part['model_num'] ?? '' }}"
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

                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-500">Inventory number</label>
                                <input type="text" name="parts[{{ $i }}][inventory_number]"
                                    value="{{ $part['inventory_number'] ?? '' }}"
                                    class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-500">Serial number</label>
                                <input type="text" name="parts[{{ $i }}][serial_number]"
                                    value="{{ $part['serial_number'] ?? '' }}"
                                    class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-sm font-medium text-gray-500">Specs</label>
                                <input type="text" name="parts[{{ $i }}][specs]"
                                       value="{{ $part['specs'] ?? '' }}"
                                       class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                            </div>

                            <div class="col-span-3">
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
    body.dragging * { cursor: crosshair !important; user-select: none; }
</style>

<script>
    let isDragging    = false;
    let dragStartX    = 0;
    let dragStartY    = 0;
    const THRESHOLD   = 5;
    const floorMapUrl = "{{ asset('images/map.png') }}";

    // For edit.blade.php use $device->pos_x and $device->pos_y
    // For create.blade.php use old('pos_x') or 50 as default
    const initialX = {{ old('pos_x', isset($device) ? $device->pos_x : 50) }};
    const initialY = {{ old('pos_y', isset($device) ? $device->pos_y : 50) }};

    // ── Map toggle ─────────────────────────────────────────
    function toggleMap() {
        const map      = document.getElementById('position-map');
        const btn      = document.getElementById('map-toggle-btn');
        const isHidden = map.classList.contains('hidden');
        map.classList.toggle('hidden', !isHidden);
        btn.textContent = isHidden ? 'Hide map' : 'Set position on map';
        if (isHidden) {
            // Wait for map to render before syncing
            requestAnimationFrame(() => {
                syncCircleFromInputs();
                switchMap();
            });
        }
    }

    // ── Switch map image based on room selection ────────────
    function switchMap() {
        const select    = document.getElementById('room_id');
        const selected  = select?.options[select.selectedIndex];
        const imageUrl  = selected ? selected.getAttribute('data-image') : '';
        const mapOuter  = document.getElementById('map-outer');
        const mapImage  = document.getElementById('map-image');
        const placeholder = document.getElementById('map-placeholder');
        const circle    = document.getElementById('drag-circle');

        if (select?.value !== '' && !imageUrl) {
            // Room selected but no image
            mapImage.classList.add('hidden');
            placeholder.classList.remove('hidden');
            circle.classList.add('hidden');
        } else if (imageUrl) {
            // Room with image — switch aspect ratio to 4:3
            mapOuter.style.aspectRatio = '1967 / 900';
            mapImage.src = imageUrl;
            mapImage.classList.remove('hidden');
            placeholder.classList.add('hidden');
            circle.classList.remove('hidden');
        } else {
            // No room — floor map
            mapOuter.style.aspectRatio = '2307 / 1559';
            mapImage.src = floorMapUrl;
            mapImage.classList.remove('hidden');
            placeholder.classList.add('hidden');
            circle.classList.remove('hidden');
        }

        // Re-sync circle after image switch
        requestAnimationFrame(() => syncCircleFromInputs());
    }

    // ── Core position calculation ───────────────────────────
    function getPosition(e) {
        const container = document.getElementById('map-outer');
        const rect      = container.getBoundingClientRect();

        let x = ((e.clientX - rect.left)  / rect.width)  * 100;
        let y = ((e.clientY - rect.top)   / rect.height) * 100;

        return {
            x: Math.max(0, Math.min(100, x)),
            y: Math.max(0, Math.min(100, y)),
        };
    }

    // ── Mouse events ────────────────────────────────────────
    document.getElementById('map-outer').addEventListener('mousedown', e => {
        if (e.target.id === 'drag-circle' ||
            e.target.id === 'map-outer'   ||
            e.target.id === 'map-image') {
            isDragging = true;
            dragStartX = e.clientX;
            dragStartY = e.clientY;
            applyPosition(e);
            e.preventDefault();
        }
    });

    document.addEventListener('mousemove', e => {
        if (!isDragging) return;
        const dx = Math.abs(e.clientX - dragStartX);
        const dy = Math.abs(e.clientY - dragStartY);
        if (dx < THRESHOLD && dy < THRESHOLD) return;
        document.body.classList.add('dragging');
        applyPosition(e);
    });

    document.addEventListener('mouseup', () => {
        isDragging = false;
        document.body.classList.remove('dragging');
    });

    // ── Apply position to inputs and circle ─────────────────
    function applyPosition(e) {
        const placeholder = document.getElementById('map-placeholder');
        if (!placeholder.classList.contains('hidden')) return;

        const { x, y } = getPosition(e);
        document.getElementById('pos_x').value = x.toFixed(3);
        document.getElementById('pos_y').value = y.toFixed(3);
        moveCircle(x, y);
    }

    function moveCircle(x, y) {
        const circle = document.getElementById('drag-circle');
        circle.style.left      = x + '%';
        circle.style.top       = y + '%';
        circle.style.transform = 'translate(-50%, -50%)';
    }

    function syncCircleFromInputs() {
        const x = parseFloat(document.getElementById('pos_x').value) || initialX;
        const y = parseFloat(document.getElementById('pos_y').value) || initialY;
        moveCircle(x, y);
    }

    // ── Init ────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        syncCircleFromInputs();
        switchMap();
    });

    // ── Parts (create.blade.php only) ───────────────────────
    @if(!isset($device))
    let partIndex = {{ old('parts') ? count(old('parts')) : 0 }};
    const statuses = @json($statuses);

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
        row.className = 'part-row grid grid-cols-3 gap-3 pb-3 border-b border-gray-100';
        row.innerHTML = `
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500">Part name</label>
                <input type="text" name="parts[${partIndex}][name]"
                       class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500">Model number</label>
                <input type="text" name="parts[${partIndex}][model_num]"
                       class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500">Status</label>
                <select name="parts[${partIndex}][status_id]"
                        class="text-sm border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                    ${statusOptions}
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500">Inventory number</label>
                <input type="text" name="parts[${partIndex}][inventory_number]"
                       class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500">Serial number</label>
                <input type="text" name="parts[${partIndex}][serial_number]"
                       class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-medium text-gray-500">Specs</label>
                <input type="text" name="parts[${partIndex}][specs]"
                       class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>
            <div class="col-span-3">
                <button type="button" onclick="removePartRow(this)"
                        class="px-3 py-1.5 text-xs font-medium border border-red-300 text-red-600 rounded-md hover:bg-red-50 transition">
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
    @endif
</script>

@endsection
