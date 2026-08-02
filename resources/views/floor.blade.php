@extends('layouts.app')

@section('title', 'Floor Layout')

@section('toolbar')
    <div class="flex items-center gap-5">
        <div class="flex items-center gap-2 text-base text-gray-500">
            <span class="w-3 h-3 rounded-full bg-green-600 inline-block"></span>
            Good: <strong class="text-gray-800">{{ $counts['good'] }}</strong>
        </div>
        <div class="flex items-center gap-2 text-base text-gray-500">
            <span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span>
            Maintenance: <strong class="text-gray-800">{{ $counts['maintenance'] }}</strong>
        </div>
        <div class="flex items-center gap-2 text-base text-gray-500">
            <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
            Out of service: <strong class="text-gray-800">{{ $counts['oos'] }}</strong>
        </div>
    </div>

    @if (auth()->user()->access_type === 'admin')
        <div class="ml-auto flex items-center gap-2">
            <button onclick="toggleDeviceEditMode()"
                    id="device-edit-mode-btn"
                    class="px-4 py-2 text-base font-medium border border-gray-300 text-gray-600 rounded-md hover:bg-gray-100 transition">
                Edit devices
            </button>
            <button onclick="toggleEditMode()"
                    id="edit-mode-btn"
                    class="px-4 py-2 text-base font-medium border border-gray-300 text-gray-600 rounded-md hover:bg-gray-100 transition">
                Edit rooms
            </button>
        </div>
    @endif
@endsection

@section('content')
<div class="mt-2 flex gap-4">

    {{-- Floor map --}}
    <div class="flex-1">
        <div id="map-wrapper"
             class="relative w-full border-2 border-green-700"
             style="aspect-ratio: 2307 / 1559;">

            <img src="{{ asset('images/map.png') }}"
                 draggable="false"
                 class="absolute inset-0 w-full h-full object-contain select-none"
                 alt="Floor map">

            {{-- Room boxes --}}
            @foreach ($rooms as $room)
                <div class="room-box absolute transition"
                     data-id="{{ $room->id }}"
                     data-name="{{ $room->name }}"
                     data-pos-x="{{ $room->pos_x }}"
                     data-pos-y="{{ $room->pos_y }}"
                     data-width="{{ $room->width }}"
                     data-height="{{ $room->height }}"
                     data-image="{{ $room->image ? asset('images/rooms/' . $room->image) : '' }}"
                     style="left: {{ $room->pos_x }}%; top: {{ $room->pos_y }}%; width: {{ $room->width }}%; height: {{ $room->height }}%;">

                    {{-- View mode content --}}
                    <a href="{{ route('floor.room', $room->id) }}"
                       class="room-link absolute inset-0 border-2 border-gray-400 bg-gray-100 hover:bg-green-50/80 hover:border-green-700 hover:text-green-700 transition rounded-md flex flex-col items-center justify-center gap-1 no-underline">
                        <span class="text-base font-medium text-center leading-tight px-1">
                            {{ $room->name }}
                        </span>
                        @if ($room->devices->isNotEmpty())
                            <div class="flex flex-wrap justify-center gap-1">
                                @foreach ($room->devices as $device)
                                    <span class="room-dot w-2 h-2 rounded-full inline-block"
                                        data-pos-x="{{ $device->pos_x }}"
                                        data-pos-y="{{ $device->pos_y }}"
                                        style="background-color: {{
                                            match($device->status->color) {
                                                'green'  => '#16a34a',
                                                'orange' => '#facc15',
                                                'red'    => '#ef4444',
                                                default  => '#9ca3af'
                                            }
                                        }};"></span>
                                @endforeach
                            </div>
                        @endif
                    </a>

                    {{-- Edit mode handles (hidden by default) --}}
                    <div class="edit-handles hidden">
                        {{-- Edge handles --}}
                        <div class="handle handle-n"  data-dir="n"></div>
                        <div class="handle handle-s"  data-dir="s"></div>
                        <div class="handle handle-e"  data-dir="e"></div>
                        <div class="handle handle-w"  data-dir="w"></div>
                        {{-- Corner handles --}}
                        <div class="handle handle-nw" data-dir="nw"></div>
                        <div class="handle handle-ne" data-dir="ne"></div>
                        <div class="handle handle-sw" data-dir="sw"></div>
                        <div class="handle handle-se" data-dir="se"></div>
                        {{-- Edit label --}}
                        <div class="edit-label absolute inset-0 flex items-center justify-center bg-blue-50/80 border-2 border-blue-400 rounded-md cursor-move select-none"
                             onclick="openRoomPanel(this.closest('.room-box'))">
                            <span class="text-sm font-medium text-blue-700 text-center px-1 pointer-events-none room-name-label">
                                {{ $room->name }}
                            </span>
                        </div>
                    </div>

                </div>
            @endforeach

            {{-- Standalone devices --}}
            @foreach ($standaloneDevices as $device)
                <div class="standalone-device absolute flex flex-col items-center gap-1 cursor-pointer group"
                    data-id="{{ $device->id }}"
                    data-pos-x="{{ $device->pos_x }}"
                    data-pos-y="{{ $device->pos_y }}"
                    style="left: {{ $device->pos_x }}%; top: {{ $device->pos_y }}%; transform: translate(-50%, -50%);"
                    onclick="handleStandaloneClick({{ $device->id }})">
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

        {{-- Edit mode action bar --}}
        <div id="edit-action-bar" class="hidden mt-3 flex items-center gap-3">
            <button onclick="saveAllRooms()"
                    class="px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                Save changes
            </button>
            <button onclick="toggleEditMode()"
                    class="px-4 py-2 text-base font-medium border border-gray-300 text-gray-500 rounded-md hover:bg-gray-100 transition">
                Done editing
            </button>
            <span id="save-status" class="text-base text-gray-400"></span>
        </div>

        {{-- Device edit mode action bar --}}
        <div id="device-edit-action-bar" class="hidden mt-3 flex items-center gap-3">
            <span class="text-base text-gray-500">Drag devices to reposition them.</span>
            <button onclick="saveAllDevicePositions()"
                    class="ml-auto px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                Save changes
            </button>
            <button onclick="toggleDeviceEditMode()"
                    class="px-4 py-2 text-base font-medium border border-gray-300 text-gray-500 rounded-md hover:bg-gray-100 transition">
                Done editing
            </button>
            <span id="device-save-status" class="text-base text-gray-400"></span>
        </div>
    </div>

    {{-- Room edit panel + Add room button, stacked in one column --}}
    <div id="room-panel-column" class="hidden w-72 flex-col gap-4 self-start">

        <div id="room-edit-panel"
             class="hidden bg-white border border-gray-200 rounded-xl p-4 flex flex-col gap-4">

            <div class="flex items-center justify-between">
                <p class="text-base font-medium text-gray-700">Edit room</p>
                <button onclick="closeRoomPanel()"
                        class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Room name</label>
                <input type="text" id="panel-name"
                       class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-500">X (%)</label>
                    <input type="number" id="panel-pos-x" step="0.001"
                           oninput="syncRoomFromPanel()"
                           class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-500">Y (%)</label>
                    <input type="number" id="panel-pos-y" step="0.001"
                           oninput="syncRoomFromPanel()"
                           class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-500">Width (%)</label>
                    <input type="number" id="panel-width" step="0.001"
                           oninput="syncRoomFromPanel()"
                           class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-500">Height (%)</label>
                    <input type="number" id="panel-height" step="0.001"
                           oninput="syncRoomFromPanel()"
                           class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Room image</label>
                <input type="file" id="panel-image" accept="image/*"
                       class="text-base text-gray-600">
                <div id="panel-image-preview" class="hidden mt-2">
                    <img id="panel-image-img" src="" alt="Room image"
                         class="w-full rounded-md border border-gray-200 object-contain"
                         style="max-height: 120px;">
                </div>
            </div>

            <button onclick="deleteRoom()"
                    class="px-4 py-2 text-base font-medium border border-red-300 text-red-600 rounded-md hover:bg-red-50 transition">
                Delete room
            </button>

        </div>

        {{-- Add room button, only visible in edit mode, stacked below the panel --}}
        <button id="add-room-btn"
                onclick="openAddRoomModal()"
                class="hidden px-4 py-2 text-base font-medium border border-green-700 text-green-700 rounded-md hover:bg-green-50 transition">
            + Add room
        </button>

    </div>

    {{-- Edit Device panel — independent of room editing, opened via device click or the view modal's Edit button --}}
    <div id="device-panel-column" class="hidden w-72 flex-col gap-4 self-start">
        <div id="device-edit-panel"
             class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col gap-4">

            <div class="flex items-center justify-between">
                <p class="text-base font-medium text-gray-700">Edit device</p>
                <button onclick="closeDeviceEditPanel()"
                        class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
            </div>

            <div id="device-panel-error" class="hidden text-base text-red-600 bg-red-50 border border-red-200 rounded-md px-3 py-2"></div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Name</label>
                <input type="text" id="device-panel-name"
                       class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Type</label>
                <select id="device-panel-type"
                        class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                    <option value="desktop">Desktop</option>
                    <option value="laptop">Laptop</option>
                    <option value="printer">Printer</option>
                    <option value="photocopier">Photocopier</option>
                    <option value="telephone">Telephone</option>
                    <option value="aircon">Aircon</option>
                    <option value="appliance">Appliance</option>
                    <option value="network">Network</option>
                    <option value="monitor">Monitor</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Status</label>
                <select id="device-panel-status"
                        class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Model number</label>
                <input type="text" id="device-panel-model"
                       class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Serial number</label>
                <input type="text" id="device-panel-serial"
                       class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Inventory number</label>
                <input type="text" id="device-panel-inventory"
                       class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Specs</label>
                <textarea id="device-panel-specs" rows="3"
                          class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700"></textarea>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="saveDeviceEdit()"
                        class="flex-1 px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                    Save
                </button>
                <span id="device-panel-status-msg" class="text-base text-gray-400"></span>
            </div>

        </div>
    </div>

</div>

{{-- Add room modal --}}
<div id="add-room-modal"
     class="fixed inset-0 z-50 flex items-center justify-center hidden"
     onclick="closeAddRoomModalOnBackdrop(event)">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative bg-white rounded-xl shadow-lg w-full max-w-md mx-4 z-10 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800">Add room</h3>
            <button onclick="closeAddRoomModal()"
                    class="text-gray-400 hover:text-gray-600 text-xl font-light leading-none">✕</button>
        </div>
        <div class="px-6 py-4 flex flex-col gap-4">
            <div id="add-room-error" class="hidden text-base text-red-600 bg-red-50 border border-red-200 rounded-md px-3 py-2"></div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Room name</label>
                <input type="text" id="add-room-name"
                       class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-500">Room image (optional)</label>
                <input type="file" id="add-room-image" accept="image/*"
                       class="text-base text-gray-600">
                <p class="text-sm text-gray-400">
                    Must match a 1961:900 aspect ratio (e.g. 1961×900px, 1307×600px). Max 5MB.
                </p>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2">
            <button onclick="closeAddRoomModal()"
                    class="px-4 py-2 text-base font-medium border border-gray-300 rounded-md hover:bg-gray-100 transition">
                Cancel
            </button>
            <button onclick="submitAddRoom()"
                    class="px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                Add room
            </button>
        </div>
    </div>
</div>

{{-- Device modal --}}
<div id="device-modal"
     class="fixed inset-0 z-50 flex items-center justify-center hidden"
     onclick="closeModal(event)">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative bg-white rounded-xl shadow-lg w-full max-w-lg mx-4 z-10 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <h3 id="modal-name" class="text-base font-semibold text-gray-800"></h3>
                <p id="modal-type" class="text-sm text-gray-400 capitalize"></p>
            </div>
            <button onclick="closeModalDirect()"
                    class="text-gray-400 hover:text-gray-600 text-xl font-light leading-none">✕</button>
        </div>
        <div class="px-6 py-3 border-b border-gray-100 flex items-center gap-2">
            <span class="text-base text-gray-500">Status:</span>
            <span id="modal-status" class="px-3 py-1 rounded-full text-sm font-medium"></span>
        </div>
        <div class="px-6 py-4 flex flex-col gap-3">
            <div class="flex gap-2">
                <span class="text-base text-gray-400 w-28 shrink-0">Serial number</span>
                <span id="modal-serial" class="text-base text-gray-700"></span>
            </div>
            <div class="flex gap-2">
                <span class="text-base text-gray-400 w-28 shrink-0">Inventory no.</span>
                <span id="modal-inventory" class="text-base text-gray-700"></span>
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
                <span class="text-base text-gray-400 w-28 shrink-0">Location</span>
                <span id="modal-room" class="text-base text-gray-700"></span>
            </div>
        </div>
        <div id="modal-parts-section" class="hidden px-6 pb-4">
            <p class="text-base font-medium text-gray-700 mb-2">Parts</p>
            <div id="modal-parts" class="flex flex-col gap-1"></div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2">
            @if (auth()->user()->access_type === 'admin')
                <button id="modal-edit-btn"
                        onclick="editDeviceFromModal()"
                        class="px-4 py-2 text-base font-medium border border-gray-300 rounded-md hover:bg-gray-100 transition">
                    Edit device
                </button>
            @endif
            <button onclick="closeModalDirect()"
                    class="px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                Close
            </button>
        </div>
    </div>
</div>

<style>
    .handle {
        position: absolute;
        width: 10px;
        height: 10px;
        background: #2563eb;
        border: 2px solid #fff;
        border-radius: 2px;
        z-index: 10;
    }
    .handle-n  { top: -5px;  left: 50%; transform: translateX(-50%); cursor: n-resize; }
    .handle-s  { bottom: -5px; left: 50%; transform: translateX(-50%); cursor: s-resize; }
    .handle-e  { right: -5px; top: 50%; transform: translateY(-50%); cursor: e-resize; }
    .handle-w  { left: -5px;  top: 50%; transform: translateY(-50%); cursor: w-resize; }
    .handle-nw { top: -5px;  left: -5px;  cursor: nw-resize; }
    .handle-ne { top: -5px;  right: -5px; cursor: ne-resize; }
    .handle-sw { bottom: -5px; left: -5px;  cursor: sw-resize; }
    .handle-se { bottom: -5px; right: -5px; cursor: se-resize; }
    body.dragging { cursor: grabbing !important; user-select: none; }
    body.resizing { user-select: none; }
    body.device-edit-active .standalone-device { cursor: grab; }
    body.device-edit-active .standalone-device > div:first-child {
        outline: 2px solid #2563eb;
        outline-offset: 2px;
    }
</style>

<script>
    const csrfToken  = "{{ csrf_token() }}";
    const storeUrl   = "{{ route('rooms.store') }}";
    const updateBase = "{{ url('/rooms') }}";
    const deleteBase = "{{ url('/rooms') }}";
    const editBaseUrl = "{{ url('/devices') }}";
    const devicePositionBase = "{{ url('/devices') }}";

    const devices    = @json($standaloneDevices->load('status', 'parts.status'));
    const statusColors = {
        green:  { bg: '#dcfce7', text: '#15803d' },
        orange: { bg: '#fef9c3', text: '#a16207' },
        red:    { bg: '#fee2e2', text: '#b91c1c' },
    };

    let editMode         = false;
    let deviceEditMode   = false;
    let activeRoom       = null;
    let dragState        = null;
    let resizeState      = null;
    let deviceDragState  = null;
    let editingDeviceId  = null;
    let suppressNextDeviceClick = false;
    const DRAG_THRESHOLD = 5;

    // ── Device edit mode ─────────────────────────────────────

    function toggleDeviceEditMode() {
        deviceEditMode = !deviceEditMode;
        const btn = document.getElementById('device-edit-mode-btn');
        const bar = document.getElementById('device-edit-action-bar');

        if (deviceEditMode) {
            btn.textContent = 'Exit edit mode';
            btn.classList.add('bg-blue-50', 'border-blue-400', 'text-blue-700');
            bar.classList.remove('hidden');
            document.body.classList.add('device-edit-active');
        } else {
            btn.textContent = 'Edit devices';
            btn.classList.remove('bg-blue-50', 'border-blue-400', 'text-blue-700');
            bar.classList.add('hidden');
            document.body.classList.remove('device-edit-active');
        }
    }

    function handleStandaloneClick(deviceId) {
        if (suppressNextDeviceClick) {
            suppressNextDeviceClick = false;
            return; // this click was the tail end of a drag — ignore it
        }
        if (deviceEditMode) {
            openDeviceEditPanel(deviceId);
            return;
        }
        openModal(deviceId);
    }

    function editDeviceFromModal() {
        if (currentModalDeviceId === null) return;
        closeModalDirect();
        openDeviceEditPanel(currentModalDeviceId);
    }

    function openDeviceEditPanel(deviceId) {
        const device = devices.find(d => d.id === deviceId);
        if (!device) return;

        document.getElementById('device-panel-error').classList.add('hidden');
        document.getElementById('device-panel-status-msg').textContent = '';

        editingDeviceId = deviceId;

        document.getElementById('device-panel-name').value      = device.name;
        document.getElementById('device-panel-type').value      = device.type;
        document.getElementById('device-panel-status').value    = device.status?.id ?? '';
        document.getElementById('device-panel-model').value     = device.model_num || '';
        document.getElementById('device-panel-serial').value    = device.serial_number || '';
        document.getElementById('device-panel-inventory').value = device.inventory_number || '';
        document.getElementById('device-panel-specs').value     = device.specs || '';

        document.getElementById('device-panel-column').classList.remove('hidden');
        document.getElementById('device-panel-column').classList.add('flex');
    }

    function closeDeviceEditPanel() {
        editingDeviceId = null;
        document.getElementById('device-panel-column').classList.add('hidden');
        document.getElementById('device-panel-column').classList.remove('flex');
    }

    async function saveDeviceEdit() {
        if (editingDeviceId === null) return;

        const errorBox = document.getElementById('device-panel-error');
        const statusMsg = document.getElementById('device-panel-status-msg');
        errorBox.classList.add('hidden');
        statusMsg.textContent = 'Saving...';

        const name = document.getElementById('device-panel-name').value.trim();
        if (!name) {
            statusMsg.textContent = '';
            errorBox.textContent = 'Name is required.';
            errorBox.classList.remove('hidden');
            return;
        }

        const payload = {
            _method:           'PUT',
            name:              name,
            type:              document.getElementById('device-panel-type').value,
            status_id:         document.getElementById('device-panel-status').value,
            model_num:         document.getElementById('device-panel-model').value,
            serial_number:     document.getElementById('device-panel-serial').value,
            inventory_number:  document.getElementById('device-panel-inventory').value,
            specs:             document.getElementById('device-panel-specs').value,
        };

        const res = await fetch(`${devicePositionBase}/${editingDeviceId}/quick-update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });

        const data = await res.json();

        if (!res.ok) {
            statusMsg.textContent = '';
            if (data.errors) {
                errorBox.textContent = Object.values(data.errors)[0][0];
            } else {
                errorBox.textContent = data.error || 'Something went wrong.';
            }
            errorBox.classList.remove('hidden');
            return;
        }

        // Update the in-memory devices array so the modal/panel reflect changes without a reload
        const idx = devices.findIndex(d => d.id === editingDeviceId);
        if (idx !== -1) {
            devices[idx] = { ...devices[idx], ...data };
        }

        // Update the dot color and tooltip label on the map
        const el = document.querySelector(`.standalone-device[data-id="${editingDeviceId}"]`);
        if (el) {
            const colorMap = { green: '#16a34a', orange: '#facc15', red: '#ef4444' };
            const dot = el.querySelector('div');
            if (dot) dot.style.backgroundColor = colorMap[data.status.color] || '#9ca3af';
            const label = el.querySelector('span');
            if (label) label.textContent = data.name;
        }

        statusMsg.textContent = 'Saved ✓';
        setTimeout(() => statusMsg.textContent = '', 3000);
    }

    async function saveAllDevicePositions() {
        const status = document.getElementById('device-save-status');
        status.textContent = 'Saving...';

        const elements = document.querySelectorAll('.standalone-device');
        const promises = Array.from(elements).map(async el => {
            const id = el.dataset.id;
            const res = await fetch(`${devicePositionBase}/${id}/position`, {
                method:  'PATCH',
                headers: {
                    'Content-Type':  'application/json',
                    'X-CSRF-TOKEN':  csrfToken,
                    'Accept':        'application/json',
                },
                body: JSON.stringify({
                    pos_x: el.dataset.posX,
                    pos_y: el.dataset.posY,
                }),
            });
            return res.json();
        });

        await Promise.all(promises);
        status.textContent = 'Saved ✓';
        setTimeout(() => status.textContent = '', 3000);
    }

    // ── Edit mode ────────────────────────────────────────────

    function toggleEditMode() {
        editMode = !editMode;
        const btn         = document.getElementById('edit-mode-btn');
        const bar         = document.getElementById('edit-action-bar');
        const panelColumn = document.getElementById('room-panel-column');
        const addRoomBtn  = document.getElementById('add-room-btn');
        const boxes       = document.querySelectorAll('.room-box');

        if (editMode) {
            btn.textContent = 'Exit edit mode';
            btn.classList.add('bg-blue-50', 'border-blue-400', 'text-blue-700');
            bar.classList.remove('hidden');
            panelColumn.classList.remove('hidden');
            panelColumn.classList.add('flex');
            addRoomBtn.classList.remove('hidden');
            boxes.forEach(box => {
                box.querySelector('.room-link').classList.add('hidden');
                box.querySelector('.edit-handles').classList.remove('hidden');
            });
        } else {
            btn.textContent = 'Edit rooms';
            btn.classList.remove('bg-blue-50', 'border-blue-400', 'text-blue-700');
            bar.classList.add('hidden');
            panelColumn.classList.add('hidden');
            panelColumn.classList.remove('flex');
            addRoomBtn.classList.add('hidden');
            boxes.forEach(box => {
                box.querySelector('.room-link').classList.remove('hidden');
                box.querySelector('.edit-handles').classList.add('hidden');
            });
            closeRoomPanel();
        }
    }

    // ── Room panel ───────────────────────────────────────────

    function openRoomPanel(box) {
        if (!editMode) return;
        activeRoom = box;

        document.getElementById('panel-name').value    = box.dataset.name;
        document.getElementById('panel-pos-x').value   = parseFloat(box.dataset.posX).toFixed(3);
        document.getElementById('panel-pos-y').value   = parseFloat(box.dataset.posY).toFixed(3);
        document.getElementById('panel-width').value   = parseFloat(box.dataset.width).toFixed(3);
        document.getElementById('panel-height').value  = parseFloat(box.dataset.height).toFixed(3);

        const preview = document.getElementById('panel-image-preview');
        const img     = document.getElementById('panel-image-img');
        if (box.dataset.image) {
            img.src = box.dataset.image;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }

        document.getElementById('room-edit-panel').classList.remove('hidden');
    }

    function closeRoomPanel() {
        activeRoom = null;
        document.getElementById('room-edit-panel').classList.add('hidden');
        document.getElementById('panel-image').value = '';
    }

    function syncRoomFromPanel() {
        if (!activeRoom) return;
        const x = parseFloat(document.getElementById('panel-pos-x').value) || 0;
        const y = parseFloat(document.getElementById('panel-pos-y').value) || 0;
        const w = parseFloat(document.getElementById('panel-width').value) || 5;
        const h = parseFloat(document.getElementById('panel-height').value) || 5;
        applyRoomGeometry(activeRoom, x, y, w, h);
    }

    function applyRoomGeometry(box, x, y, w, h) {
        box.dataset.posX   = x;
        box.dataset.posY   = y;
        box.dataset.width  = w;
        box.dataset.height = h;

        positionDevicesOnMap();
    }

    function updatePanelInputs(box) {
        if (activeRoom !== box) return;
        document.getElementById('panel-pos-x').value  = parseFloat(box.dataset.posX).toFixed(3);
        document.getElementById('panel-pos-y').value  = parseFloat(box.dataset.posY).toFixed(3);
        document.getElementById('panel-width').value  = parseFloat(box.dataset.width).toFixed(3);
        document.getElementById('panel-height').value = parseFloat(box.dataset.height).toFixed(3);
    }

    // ── Add room (modal) ─────────────────────────────────────

    function openAddRoomModal() {
        document.getElementById('add-room-error').classList.add('hidden');
        document.getElementById('add-room-name').value = '';
        document.getElementById('add-room-image').value = '';
        document.getElementById('add-room-modal').classList.remove('hidden');
    }

    function closeAddRoomModal() {
        document.getElementById('add-room-modal').classList.add('hidden');
    }

    function closeAddRoomModalOnBackdrop(event) {
        if (event.target === document.getElementById('add-room-modal')) closeAddRoomModal();
    }

    async function submitAddRoom() {
        const name      = document.getElementById('add-room-name').value.trim();
        const imageFile = document.getElementById('add-room-image').files[0];
        const errorBox  = document.getElementById('add-room-error');

        errorBox.classList.add('hidden');

        if (!name) {
            errorBox.textContent = 'Room name is required.';
            errorBox.classList.remove('hidden');
            return;
        }

        const formData = new FormData();
        formData.append('name', name);
        if (imageFile) formData.append('image', imageFile);

        const res = await fetch(storeUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        });

        const data = await res.json();

        if (!res.ok) {
            if (data.errors) {
                errorBox.textContent = Object.values(data.errors)[0][0];
            } else {
                errorBox.textContent = data.error || 'Something went wrong.';
            }
            errorBox.classList.remove('hidden');
            return;
        }

        closeAddRoomModal();
        createRoomBox(data);
    }

    function createRoomBox(room) {
        const wrapper = document.getElementById('map-wrapper');
        const box     = document.createElement('div');
        box.className  = 'room-box absolute transition';
        box.dataset.id     = room.id;
        box.dataset.name   = room.name;
        box.dataset.posX   = room.pos_x;
        box.dataset.posY   = room.pos_y;
        box.dataset.width  = room.width;
        box.dataset.height = room.height;
        box.dataset.image  = room.image ? `/images/rooms/${room.image}` : '';
        box.style.left     = room.pos_x + '%';
        box.style.top      = room.pos_y + '%';
        box.style.width    = room.width + '%';
        box.style.height   = room.height + '%';

        box.innerHTML = `
            <a class="room-link hidden absolute inset-0 border-2 border-gray-400 bg-gray-100 rounded-md flex items-center justify-center">
                <span class="text-sm font-medium text-center px-1">${room.name}</span>
            </a>
            <div class="edit-handles">
                <div class="handle handle-n"  data-dir="n"></div>
                <div class="handle handle-s"  data-dir="s"></div>
                <div class="handle handle-e"  data-dir="e"></div>
                <div class="handle handle-w"  data-dir="w"></div>
                <div class="handle handle-nw" data-dir="nw"></div>
                <div class="handle handle-ne" data-dir="ne"></div>
                <div class="handle handle-sw" data-dir="sw"></div>
                <div class="handle handle-se" data-dir="se"></div>
                <div class="edit-label absolute inset-0 flex items-center justify-center bg-blue-50/80 border-2 border-blue-400 rounded-md cursor-move select-none"
                     onclick="openRoomPanel(this.closest('.room-box'))">
                    <span class="text-sm font-medium text-blue-700 text-center px-1 pointer-events-none room-name-label">
                        ${room.name}
                    </span>
                </div>
            </div>
        `;

        wrapper.appendChild(box);
        openRoomPanel(box);
        positionDevicesOnMap();
    }

    // ── Save all rooms ───────────────────────────────────────

    async function saveAllRooms() {
        const status = document.getElementById('save-status');
        status.textContent = 'Saving...';

        const boxes = document.querySelectorAll('.room-box');
        const promises = Array.from(boxes).map(async box => {
            const id      = box.dataset.id;
            const formData = new FormData();
            formData.append('_method',  'PUT');
            formData.append('name',     document.getElementById('panel-name')?.value && activeRoom === box
                ? document.getElementById('panel-name').value
                : box.dataset.name);
            formData.append('pos_x',   box.dataset.posX);
            formData.append('pos_y',   box.dataset.posY);
            formData.append('width',   box.dataset.width);
            formData.append('height',  box.dataset.height);

            const imageInput = document.getElementById('panel-image');
            if (activeRoom === box && imageInput.files.length > 0) {
                formData.append('image', imageInput.files[0]);
            }

            const res = await fetch(`${updateBase}/${id}`, {
                method:  'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                body:    formData,
            });

            const data = await res.json();

            // Update box name label
            box.dataset.name = data.name;
            box.querySelectorAll('.room-name-label').forEach(el => el.textContent = data.name);
            if (data.image) box.dataset.image = `/images/rooms/${data.image}`;

            return data;
        });

        await Promise.all(promises);
        status.textContent = 'Saved ✓';
        setTimeout(() => status.textContent = '', 3000);
    }

    // ── Delete room ──────────────────────────────────────────

    async function deleteRoom() {
        if (!activeRoom) return;
        if (!confirm(`Delete "${activeRoom.dataset.name}"?`)) return;

        const id  = activeRoom.dataset.id;
        const res = await fetch(`${deleteBase}/${id}`, {
            method:  'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept':       'application/json',
            },
        });

        const data = await res.json();

        if (data.error) {
            alert(data.error);
            return;
        }

        activeRoom.remove();
        closeRoomPanel();
    }

    // ── Drag to move ─────────────────────────────────────────

    document.addEventListener('mousedown', e => {
        if (deviceEditMode) {
            const deviceEl = e.target.closest('.standalone-device');
            if (deviceEl) {
                const wrapper = document.getElementById('map-wrapper');
                const rect    = wrapper.getBoundingClientRect();

                deviceDragState = {
                    el:        deviceEl,
                    startX:    e.clientX,
                    startY:    e.clientY,
                    startPosX: parseFloat(deviceEl.dataset.posX),
                    startPosY: parseFloat(deviceEl.dataset.posY),
                    wrapW:     rect.width,
                    wrapH:     rect.height,
                    started:   false,
                };
                e.preventDefault();
                return;
            }
        }

        if (!editMode) return;

        const label = e.target.closest('.edit-label');
        const handle = e.target.closest('.handle');

        if (handle) {
            // Resize
            const box     = handle.closest('.room-box');
            const wrapper = document.getElementById('map-wrapper');
            const rect    = wrapper.getBoundingClientRect();

            resizeState = {
                box,
                dir:       handle.dataset.dir,
                startX:    e.clientX,
                startY:    e.clientY,
                startPosX: parseFloat(box.dataset.posX),
                startPosY: parseFloat(box.dataset.posY),
                startW:    parseFloat(box.dataset.width),
                startH:    parseFloat(box.dataset.height),
                wrapW:     rect.width,
                wrapH:     rect.height,
                moved:     false,
            };
            document.body.classList.add('resizing');
            e.preventDefault();
            return;
        }

        if (label) {
            // Drag to move
            const box     = label.closest('.room-box');
            const wrapper = document.getElementById('map-wrapper');
            const rect    = wrapper.getBoundingClientRect();

            dragState = {
                box,
                startX:    e.clientX,
                startY:    e.clientY,
                startPosX: parseFloat(box.dataset.posX),
                startPosY: parseFloat(box.dataset.posY),
                wrapW:     rect.width,
                wrapH:     rect.height,
                started:   false,
            };
            e.preventDefault();
        }
    });

    document.addEventListener('mousemove', e => {
        if (deviceDragState) {
            const dx = e.clientX - deviceDragState.startX;
            const dy = e.clientY - deviceDragState.startY;

            if (!deviceDragState.started) {
                if (Math.abs(dx) < DRAG_THRESHOLD && Math.abs(dy) < DRAG_THRESHOLD) return;
                deviceDragState.started = true;
                document.body.classList.add('dragging');
            }

            const newX = Math.max(0, Math.min(100,
                deviceDragState.startPosX + (dx / deviceDragState.wrapW) * 100));
            const newY = Math.max(0, Math.min(100,
                deviceDragState.startPosY + (dy / deviceDragState.wrapH) * 100));

            deviceDragState.el.dataset.posX = newX;
            deviceDragState.el.dataset.posY = newY;
            positionDevicesOnMap();
        }

        if (dragState) {
            const dx = e.clientX - dragState.startX;
            const dy = e.clientY - dragState.startY;

            if (!dragState.started) {
                if (Math.abs(dx) < DRAG_THRESHOLD && Math.abs(dy) < DRAG_THRESHOLD) return;
                dragState.started = true;
                document.body.classList.add('dragging');
            }

            const box  = dragState.box;
            const newX = Math.max(0, Math.min(100 - parseFloat(box.dataset.width),
                dragState.startPosX + (dx / dragState.wrapW) * 100));
            const newY = Math.max(0, Math.min(100 - parseFloat(box.dataset.height),
                dragState.startPosY + (dy / dragState.wrapH) * 100));

            applyRoomGeometry(box, newX, newY, parseFloat(box.dataset.width), parseFloat(box.dataset.height));
            updatePanelInputs(box);
        }

        if (resizeState) {
            const { box, dir, startX, startY, startPosX, startPosY, startW, startH, wrapW, wrapH } = resizeState;

            if (!resizeState.moved) {
                const dx = Math.abs(e.clientX - startX);
                const dy = Math.abs(e.clientY - startY);
                if (dx < DRAG_THRESHOLD && dy < DRAG_THRESHOLD) return;
                resizeState.moved = true;
            }

            const dx = ((e.clientX - startX) / wrapW) * 100;
            const dy = ((e.clientY - startY) / wrapH) * 100;

            let x = startPosX, y = startPosY, w = startW, h = startH;
            const MIN = 3;

            if (dir.includes('e')) w = Math.max(MIN, startW + dx);
            if (dir.includes('s')) h = Math.max(MIN, startH + dy);
            if (dir.includes('w')) { w = Math.max(MIN, startW - dx); x = startPosX + (startW - w); }
            if (dir.includes('n')) { h = Math.max(MIN, startH - dy); y = startPosY + (startH - h); }

            applyRoomGeometry(box, x, y, w, h);
            updatePanelInputs(box);
        }
    });

    document.addEventListener('mouseup', () => {
        if (deviceDragState && deviceDragState.started) {
            suppressNextDeviceClick = true;
        }
        dragState       = null;
        resizeState     = null;
        deviceDragState = null;
        document.body.classList.remove('dragging', 'resizing');
    });

    function positionDevicesOnMap() {
        const wrapper = document.getElementById('map-wrapper');
        const img     = wrapper.querySelector('img');
        const cRect   = wrapper.getBoundingClientRect();

        const cW = cRect.width;
        const cH = cRect.height;
        const iW = img.naturalWidth;
        const iH = img.naturalHeight;

        const scale     = Math.min(cW / iW, cH / iH);
        const renderedW = iW * scale;
        const renderedH = iH * scale;
        const offsetX   = (cW - renderedW) / 2;
        const offsetY   = (cH - renderedH) / 2;

        // Position standalone device circles
        document.querySelectorAll('.standalone-device').forEach(el => {
            const x = parseFloat(el.dataset.posX);
            const y = parseFloat(el.dataset.posY);

            const pixelX = offsetX + (x / 100) * renderedW;
            const pixelY = offsetY + (y / 100) * renderedH;

            el.style.left      = pixelX + 'px';
            el.style.top       = pixelY + 'px';
            el.style.transform = 'translate(-50%, -50%)';
        });

        // Position room boxes
        document.querySelectorAll('.room-box').forEach(box => {
            const x = parseFloat(box.dataset.posX);
            const y = parseFloat(box.dataset.posY);
            const w = parseFloat(box.dataset.width);
            const h = parseFloat(box.dataset.height);

            box.style.left   = (offsetX + (x / 100) * renderedW) + 'px';
            box.style.top    = (offsetY + (y / 100) * renderedH) + 'px';
            box.style.width  = (w / 100) * renderedW + 'px';
            box.style.height = (h / 100) * renderedH + 'px';
        });
    }

    // Run after image loads and on window resize
    const mapImg = document.querySelector('#map-wrapper img');
    function initializeMap() {
        requestAnimationFrame(() => {
            positionDevicesOnMap();
        });
    }

    if (mapImg.complete) {
        initializeMap();
    } else {
        mapImg.addEventListener('load', initializeMap);
    }
    const wrapper = document.getElementById('map-wrapper');

    new ResizeObserver(() => {
        positionDevicesOnMap();
    }).observe(wrapper);

    window.addEventListener('resize', positionDevicesOnMap);

    // ── Device modal ─────────────────────────────────────────

    let currentModalDeviceId = null;

    function openModal(deviceId) {
        const device = devices.find(d => d.id === deviceId);
        if (!device) return;

        currentModalDeviceId = deviceId;

        document.getElementById('modal-name').textContent  = device.name;
        document.getElementById('modal-type').textContent  = device.type;
        document.getElementById('modal-serial').textContent = device.serial_number || '—';
        document.getElementById('modal-inventory').textContent = device.inventory_number || '—';
        document.getElementById('modal-model').textContent = device.model_num || '—';
        document.getElementById('modal-specs').textContent = device.specs     || '—';
        document.getElementById('modal-room').textContent  = 'Standalone';

        const color  = device.status?.color || 'default';
        const colors = statusColors[color] || { bg: '#f3f4f6', text: '#6b7280' };
        const badge  = document.getElementById('modal-status');
        badge.textContent      = device.status?.label || '—';
        badge.style.background = colors.bg;
        badge.style.color      = colors.text;

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
        if (event.target === document.getElementById('device-modal')) closeModalDirect();
    }

    function closeModalDirect() {
        document.getElementById('device-modal').classList.add('hidden');
    }
</script>

@endsection
