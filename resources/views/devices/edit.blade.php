@extends('layouts.app')

@section('title', 'Edit ' . $device->name)

@section('toolbar')
    <a href="{{ route('devices.index') }}"
       class="flex items-center gap-2 text-base text-gray-500 hover:text-green-700 transition">
        ← Back to device list
    </a>
    <span class="text-base font-medium text-gray-800">Edit — {{ $device->name }}</span>
    <a href="{{ route('maintenance.create.device', $device->id) }}"
       class="px-4 py-2 text-base font-medium border border-green-700 text-green-700 rounded-md hover:bg-green-50 transition ml-auto">
        + Log maintenance
    </a>
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

    <form method="POST" action="{{ route('devices.update', $device->id) }}"
          class="flex flex-col gap-4">
        @csrf
        @method('PUT')

        <div class="bg-white border border-gray-200 rounded-xl p-6">
            <div class="grid grid-cols-2 gap-6">

                {{-- Column 1 --}}
                <div class="flex flex-col gap-4">

                    <div class="flex flex-col gap-1">
                        <label class="text-base font-medium text-gray-700">Name</label>
                        <input type="text" name="name" value="{{ old('name', $device->name) }}"
                               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-base font-medium text-gray-700">Type</label>
                        <select name="type"
                                class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                            @foreach (['desktop','laptop','printer','photocopier','telephone','aircon','appliance','network','monitor','other'] as $type)
                                <option value="{{ $type }}" {{ old('type', $device->type) === $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-base font-medium text-gray-700">Model</label>
                        <input type="text" name="model_num" value="{{ old('model_num', $device->model_num) }}"
                               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-base font-medium text-gray-700">Serial number</label>
                        <input type="text" name="serial_number"
                            value="{{ old('serial_number', $device->serial_number) }}"
                            class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-base font-medium text-gray-700">Inventory number</label>
                        <input type="text" name="inventory_number"
                            value="{{ old('inventory_number', $device->inventory_number) }}"
                            class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-base font-medium text-gray-700">Status</label>
                        <select name="status_id"
                                class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}" {{ old('status_id', $device->status_id) == $status->id ? 'selected' : '' }}>
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
                            <optgroup label="Floor Layout Rooms">
                                @foreach ($rooms->where('is_storage', false) as $room)
                                    <option value="{{ $room->id }}"
                                            data-is-storage="0"
                                            data-image="{{ $room->image ? asset('images/rooms/' . $room->image) : '' }}"
                                            {{ old('room_id', $device->room_id) == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Storage / Dump (No map location)">
                                @foreach ($rooms->where('is_storage', true) as $room)
                                    <option value="{{ $room->id }}"
                                            data-is-storage="1"
                                            data-image=""
                                            {{ old('room_id', $device->room_id) == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div id="storage-room-note" class="hidden px-3 py-2 bg-gray-50 border border-gray-200 rounded-md text-sm text-gray-600">
                        📦 Storage room devices do not require a map location.
                    </div>

                    <div id="position-inputs-container" class="flex flex-col gap-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex flex-col gap-1">
                                <label class="text-base font-medium text-gray-700">Position X (%)</label>
                                <input type="number" name="pos_x" id="pos_x" step="0.001"
                                       value="{{ old('pos_x', $device->pos_x) }}"
                                       oninput="syncCircleFromInputs()"
                                       class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-base font-medium text-gray-700">Position Y (%)</label>
                                <input type="number" name="pos_y" id="pos_y" step="0.001"
                                       value="{{ old('pos_y', $device->pos_y) }}"
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
                    </div>

                    {{-- Sub parts --}}
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="sub_parts" value="0">
                        <input type="checkbox" name="sub_parts" id="sub_parts" value="1"
                               {{ old('sub_parts', $device->sub_parts) ? 'checked' : '' }}
                               class="w-4 h-4 accent-green-700">
                        <label for="sub_parts" class="text-base font-medium text-gray-700">Has sub parts</label>
                    </div>

                    {{-- Specs --}}
                    <div class="flex flex-col gap-1 flex-1">
                        <label class="text-base font-medium text-gray-700">Specs</label>
                        <textarea name="specs" rows="4"
                                  class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700 resize-none">{{ old('specs', $device->specs) }}</textarea>
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
                <div id="map-outer" class="relative w-full border-2 border-green-700 overflow-hidden"
                    style="aspect-ratio: 2307 / 1559;">

                    <img id="map-image"
                        src="{{ asset('images/map.png') }}"
                        draggable="false"
                        class="absolute inset-0 w-full h-full object-contain select-none pointer-events-none"
                        alt="Map"
                        id="map-img">

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

        {{-- Submit --}}
        <div class="flex gap-3">
            <button type="submit"
                    class="px-5 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                Save changes
            </button>
            <a href="{{ route('devices.index') }}"
               class="px-5 py-2 text-base font-medium border border-gray-300 text-gray-500 rounded-md hover:bg-gray-100 transition">
                Cancel
            </a>
        </div>

    </form>

    {{-- Sub parts section outside main form --}}
    @if ($device->sub_parts)
        <div class="mt-4 bg-white border border-gray-200 rounded-xl p-6 flex flex-col gap-4">
            <p class="text-base font-medium text-gray-700">Parts</p>

            @forelse ($device->parts as $part)
                <form method="POST"
                      action="{{ route('devices.parts.update', [$device->id, $part->id]) }}"
                      class="grid grid-cols-3 gap-3 pb-4 border-b border-gray-100">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500">Part name</label>
                        <input type="text" name="name" value="{{ old('name', $part->name) }}"
                               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500">Model</label>
                        <input type="text" name="model_num" value="{{ old('model_num', $part->model_num) }}"
                               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <select name="status_id"
                                class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}" {{ old('status_id', $part->status_id) == $status->id ? 'selected' : '' }}>
                                    {{ $status->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500">Inventory number</label>
                        <input type="text" name="inventory_number"
                            value="{{ old('inventory_number', $part->inventory_number) }}"
                            class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500">Serial number</label>
                        <input type="text" name="serial_number"
                            value="{{ old('serial_number', $part->serial_number) }}"
                            class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500">Specs</label>
                        <input type="text" name="specs" value="{{ old('specs', $part->specs) }}"
                               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="col-span-3 flex gap-2">
                        <button type="submit"
                                class="px-4 py-1.5 text-sm font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                            Save part
                        </button>
                        <a href="#"
                           onclick="event.preventDefault(); if(confirm('Delete {{ $part->name }}?')) document.getElementById('delete-part-{{ $part->id }}').submit();"
                           class="px-4 py-1.5 text-sm font-medium border border-red-300 text-red-600 rounded-md hover:bg-red-50 transition">
                            Delete part
                        </a>
                    </div>

                </form>

                <form id="delete-part-{{ $part->id }}"
                      method="POST"
                      action="{{ route('devices.parts.destroy', [$device->id, $part->id]) }}"
                      class="hidden">
                    @csrf
                    @method('DELETE')
                </form>

            @empty
                <p class="text-base text-gray-400">No parts yet.</p>
            @endforelse

            {{-- Add new part --}}
            <div class="pt-2">
                <p class="text-base font-medium text-gray-700 mb-3">Add new part</p>
                <form method="POST"
                      action="{{ route('devices.parts.store', $device->id) }}"
                      class="grid grid-cols-3 gap-3">
                    @csrf

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500">Part name</label>
                        <input type="text" name="name" placeholder="e.g. Monitor"
                               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500">Model number</label>
                        <input type="text" name="model_num" placeholder="Optional"
                               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500">Status</label>
                        <select name="status_id"
                                class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}">{{ $status->label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500">Inventory number</label>
                        <input type="text" name="inventory_number" placeholder="Optional"
                               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500">Serial number</label>
                        <input type="text" name="serial_number" placeholder="Optional"
                               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-gray-500">Specs</label>
                        <input type="text" name="specs" placeholder="Optional"
                               class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    </div>

                    <div class="col-span-3">
                        <button type="submit"
                                class="px-4 py-1.5 text-sm font-medium border border-green-700 text-green-700 rounded-md hover:bg-green-50 transition">
                            + Add part
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

    {{-- Device Update Logs / History Section --}}
    <div class="mt-4 bg-white border border-gray-200 rounded-xl p-6 flex flex-col gap-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-800">Update & Activity History</h3>
            <span class="text-xs text-gray-400">{{ $device->updateLogs->count() }} event(s) recorded</span>
        </div>

        <div class="divide-y divide-gray-100 overflow-y-auto max-h-60 pr-1">
            @forelse ($device->updateLogs as $log)
                <div class="py-2.5 flex flex-col gap-1 text-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium border
                                {{ match($log->action) {
                                    'created' => 'bg-green-50 text-green-700 border-green-200',
                                    'updated', 'quick_updated' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'status_changed' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'moved' => 'bg-teal-50 text-teal-700 border-teal-200',
                                    'part_added', 'part_updated', 'part_deleted' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'maintenance' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    default => 'bg-gray-50 text-gray-700 border-gray-200'
                                } }}">
                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                            </span>
                            <span class="text-gray-700">{{ $log->description }}</span>
                        </div>
                        <span class="text-xs text-gray-400 whitespace-nowrap">{{ $log->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if ($log->user)
                        <span class="text-xs text-gray-400">By: {{ $log->user->username }} ({{ $log->user->access_type }})</span>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-400 py-3">No activity logs recorded for this device yet.</p>
            @endforelse
        </div>
    </div>

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
    const initialX = {{ old('pos_x', $device->pos_x ?? 50) }};
    const initialY = {{ old('pos_y', $device->pos_y ?? 50) }};

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
        const isStorage = selected ? selected.getAttribute('data-is-storage') === '1' : false;
        const imageUrl  = selected ? selected.getAttribute('data-image') : '';
        const mapOuter  = document.getElementById('map-outer');
        const mapImage  = document.getElementById('map-image');
        const placeholder = document.getElementById('map-placeholder');
        const circle    = document.getElementById('drag-circle');
        const posInputs = document.getElementById('position-inputs-container');
        const storeNote = document.getElementById('storage-room-note');
        const mapWrapper = document.getElementById('position-map');

        if (isStorage) {
            if (posInputs) posInputs.classList.add('hidden');
            if (storeNote) storeNote.classList.remove('hidden');
            document.getElementById('pos_x').value = '';
            document.getElementById('pos_y').value = '';
            if (mapWrapper && !mapWrapper.classList.contains('hidden')) {
                toggleMap();
            }
            return;
        } else {
            if (posInputs) posInputs.classList.remove('hidden');
            if (storeNote) storeNote.classList.add('hidden');
        }

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
        const img       = document.getElementById('map-image');
        const cRect     = container.getBoundingClientRect();

        const cW = cRect.width;
        const cH = cRect.height;
        const iW = img.naturalWidth;
        const iH = img.naturalHeight;

        const scale     = Math.min(cW / iW, cH / iH);
        const renderedW = iW * scale;
        const renderedH = iH * scale;
        const offsetX   = (cW - renderedW) / 2;
        const offsetY   = (cH - renderedH) / 2;

        let x = ((e.clientX - cRect.left - offsetX) / renderedW) * 100;
        let y = ((e.clientY - cRect.top  - offsetY) / renderedH) * 100;

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
        const container = document.getElementById('map-outer');
        const img       = document.getElementById('map-image');
        const cRect     = container.getBoundingClientRect();

        const cW = cRect.width;
        const cH = cRect.height;
        const iW = img.naturalWidth;
        const iH = img.naturalHeight;

        const scale     = Math.min(cW / iW, cH / iH);
        const renderedW = iW * scale;
        const renderedH = iH * scale;
        const offsetX   = (cW - renderedW) / 2;
        const offsetY   = (cH - renderedH) / 2;

        const pixelX = offsetX + (x / 100) * renderedW;
        const pixelY = offsetY + (y / 100) * renderedH;

        const circle = document.getElementById('drag-circle');
        circle.style.left      = pixelX + 'px';
        circle.style.top       = pixelY + 'px';
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
</script>

@endsection
