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
        <button onclick="toggleEditMode()"
                id="edit-mode-btn"
                class="ml-auto px-4 py-2 text-base font-medium border border-gray-300 text-gray-600 rounded-md hover:bg-gray-100 transition">
            Edit rooms
        </button>
    @endif
@endsection

@section('content')
<div class="mt-2 flex gap-4">

    {{-- Floor map --}}
    <div class="flex-1">
        <div id="map-wrapper"
             class="relative w-full border-2 border-green-700"
             style="aspect-ratio: 1420 / 651;">

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
                     style="
                         left: {{ $room->pos_x }}%;
                         top: {{ $room->pos_y }}%;
                         width: {{ $room->width }}%;
                         height: {{ $room->height }}%;">

                    {{-- View mode content --}}
                    <a href="{{ route('floor.room', $room->id) }}"
                       class="room-link absolute inset-0 border-2 border-gray-400 bg-gray-100 hover:bg-green-50/80 hover:border-green-700 hover:text-green-700 transition rounded-md flex flex-col items-center justify-center gap-1 no-underline">
                        <span class="text-base font-medium text-center leading-tight px-1">
                            {{ $room->name }}
                        </span>
                        @if ($room->devices->isNotEmpty())
                            <div class="flex flex-wrap justify-center gap-1">
                                @foreach ($room->devices as $device)
                                    <span class="w-2 h-2 rounded-full inline-block"
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

        {{-- Edit mode action bar --}}
        <div id="edit-action-bar" class="hidden mt-3 flex items-center gap-3">
            <button onclick="addRoom()"
                    class="px-4 py-2 text-base font-medium border border-green-700 text-green-700 rounded-md hover:bg-green-50 transition">
                + Add room
            </button>
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
    </div>

    {{-- Room edit panel (hidden by default) --}}
    <div id="room-edit-panel"
         class="hidden w-72 bg-white border border-gray-200 rounded-xl p-4 flex flex-col gap-4 self-start">

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
</style>

<script>
    const csrfToken  = "{{ csrf_token() }}";
    const storeUrl   = "{{ route('rooms.store') }}";
    const updateBase = "{{ url('/rooms') }}";
    const deleteBase = "{{ url('/rooms') }}";
    const editBaseUrl = "{{ url('/devices') }}";

    const devices    = @json($standaloneDevices->load('status', 'parts.status'));
    const statusColors = {
        green:  { bg: '#dcfce7', text: '#15803d' },
        orange: { bg: '#fef9c3', text: '#a16207' },
        red:    { bg: '#fee2e2', text: '#b91c1c' },
    };

    let editMode        = false;
    let activeRoom      = null;
    let dragState       = null;
    let resizeState     = null;
    const DRAG_THRESHOLD = 5;

    // ── Edit mode ────────────────────────────────────────────

    function toggleEditMode() {
        editMode = !editMode;
        const btn     = document.getElementById('edit-mode-btn');
        const bar     = document.getElementById('edit-action-bar');
        const boxes   = document.querySelectorAll('.room-box');

        if (editMode) {
            btn.textContent = 'Exit edit mode';
            btn.classList.add('bg-blue-50', 'border-blue-400', 'text-blue-700');
            bar.classList.remove('hidden');
            boxes.forEach(box => {
                box.querySelector('.room-link').classList.add('hidden');
                box.querySelector('.edit-handles').classList.remove('hidden');
            });
        } else {
            btn.textContent = 'Edit rooms';
            btn.classList.remove('bg-blue-50', 'border-blue-400', 'text-blue-700');
            bar.classList.add('hidden');
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
        box.style.left   = x + '%';
        box.style.top    = y + '%';
        box.style.width  = w + '%';
        box.style.height = h + '%';
        box.dataset.posX  = x;
        box.dataset.posY  = y;
        box.dataset.width = w;
        box.dataset.height = h;
    }

    function updatePanelInputs(box) {
        if (activeRoom !== box) return;
        document.getElementById('panel-pos-x').value  = parseFloat(box.dataset.posX).toFixed(3);
        document.getElementById('panel-pos-y').value  = parseFloat(box.dataset.posY).toFixed(3);
        document.getElementById('panel-width').value  = parseFloat(box.dataset.width).toFixed(3);
        document.getElementById('panel-height').value = parseFloat(box.dataset.height).toFixed(3);
    }

    // ── Add room ─────────────────────────────────────────────

    async function addRoom() {
        const name = prompt('Room name:');
        if (!name) return;

        const res  = await fetch(storeUrl, {
            method:  'POST',
            headers: {
                'Content-Type':  'application/json',
                'X-CSRF-TOKEN':  csrfToken,
                'Accept':        'application/json',
            },
            body: JSON.stringify({ name }),
        });

        const room = await res.json();
        createRoomBox(room);
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
        box.dataset.image  = '';
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
        dragState   = null;
        resizeState = null;
        document.body.classList.remove('dragging', 'resizing');
    });

    // ── Device modal ─────────────────────────────────────────

    function openModal(deviceId) {
        const device = devices.find(d => d.id === deviceId);
        if (!device) return;

        document.getElementById('modal-name').textContent  = device.name;
        document.getElementById('modal-type').textContent  = device.type;
        document.getElementById('modal-serial').textContent = device.serial_number || '—';
        document.getElementById('modal-model').textContent = device.model_num || '—';
        document.getElementById('modal-specs').textContent = device.specs     || '—';
        document.getElementById('modal-room').textContent  = 'Standalone';

        const color  = device.status?.color || 'default';
        const colors = statusColors[color] || { bg: '#f3f4f6', text: '#6b7280' };
        const badge  = document.getElementById('modal-status');
        badge.textContent      = device.status?.label || '—';
        badge.style.background = colors.bg;
        badge.style.color      = colors.text;

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
        if (event.target === document.getElementById('device-modal')) closeModalDirect();
    }

    function closeModalDirect() {
        document.getElementById('device-modal').classList.add('hidden');
    }
</script>

@endsection
