@extends('layouts.app')

@section('title', $room->name)

@section('toolbar')
    <a href="{{ route('floor.index') }}"
       class="flex items-center gap-2 text-base text-gray-500 hover:text-green-700 transition">
        ← Back to floor layout
    </a>
    <span class="text-base font-medium text-gray-800">{{ $room->name }}</span>

    @if (auth()->user()->access_type === 'admin')
        <button onclick="toggleDeviceEditMode()"
                id="device-edit-mode-btn"
                class="ml-auto px-4 py-2 text-base font-medium border border-gray-300 text-gray-600 rounded-md hover:bg-gray-100 transition">
            Edit devices
        </button>
    @endif
@endsection

@section('content')
<div class="mt-4 flex gap-6 items-start">

    {{-- Room image / layout map --}}
    <div class="flex-1">
        <div id="room-map-wrapper"
             class="border-2 border-green-700 rounded-xl overflow-hidden relative shadow-sm"
             style="aspect-ratio: 1961 / 900;">

            @if ($room->image)
                <img id="room-map-img"
                     src="{{ asset('images/rooms/' . $room->image) }}"
                     draggable="false"
                     class="absolute inset-0 w-full h-full object-contain select-none"
                     alt="{{ $room->name }} layout">
            @else
                <div class="absolute inset-0 flex items-center justify-center text-gray-300 text-base">
                    No room layout image available.
                </div>
            @endif

            {{-- Device circles on map --}}
            @foreach ($room->devices as $device)
                <div class="room-device absolute flex flex-col items-center gap-1 cursor-pointer group"
                     data-id="{{ $device->id }}"
                     data-pos-x="{{ $device->pos_x }}"
                     data-pos-y="{{ $device->pos_y }}"
                     style="left: {{ $device->pos_x }}%; top: {{ $device->pos_y }}%; transform: translate(-50%, -50%);"
                     onclick="handleDeviceClick({{ $device->id }})">

                    <div class="w-5 h-5 rounded-full border-2 border-white transition group-hover:scale-125 shadow-sm"
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
                                 transition whitespace-nowrap border border-gray-200 pointer-events-none z-20">
                        {{ $device->name }}
                    </span>
                </div>
            @endforeach

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

    {{-- Device list panel (scrollable container for consistent page height) --}}
    <div id="device-list-card"
         class="w-80 bg-white border border-gray-200 rounded-xl p-4 flex flex-col gap-3 shadow-sm h-[calc(100vh-14rem)] min-h-[520px]">
        <div>
            <h2 class="text-base font-semibold text-gray-800">{{ $room->name }} devices</h2>
            <p class="text-sm text-gray-400">{{ $room->devices->count() }} device(s) total</p>
        </div>

        <input type="text"
               id="device-search"
               placeholder="Search devices..."
               oninput="filterDevices()"
               class="text-base border border-gray-300 rounded-md px-3 py-2 w-full focus:outline-none focus:ring-1 focus:ring-green-700">

        {{-- Scrollable device list --}}
        <div class="flex flex-col divide-y divide-gray-100 overflow-y-auto flex-1 pr-1" id="device-list">
            @forelse ($room->devices as $device)
                <div class="device-row py-2.5 flex flex-col gap-1 cursor-pointer hover:bg-gray-50 px-2 rounded transition"
                     data-id="{{ $device->id }}"
                     onclick="handleDeviceClick({{ $device->id }})">
                    <div class="flex justify-between items-center">
                        <span class="text-base font-medium text-gray-800 device-name">{{ $device->name }}</span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full device-status-label"
                              style="
                                  background-color: {{
                                      match($device->status->color) {
                                          'green'  => '#dcfce7',
                                          'orange' => '#fef9c3',
                                          'red'    => '#fee2e2',
                                          default  => '#f3f4f6'
                                      }
                                  }};
                                  color: {{
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
                </div>
            @empty
                <p class="text-base text-gray-400 py-4 text-center">No devices in this room.</p>
            @endforelse
        </div>

        <a href="{{ route('devices.index', ['room' => $room->id]) }}"
           class="mt-auto w-full text-center px-4 py-2 text-base font-medium border border-gray-300 rounded-md hover:bg-gray-100 transition no-underline text-gray-700">
            View full device list
        </a>
    </div>

    {{-- Edit Device panel — includes Sub-parts and internal scrollbar --}}
    <div id="device-panel-column" class="hidden w-80 flex-col self-start">
        <div id="device-edit-panel"
             class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col gap-3 shadow-sm h-[calc(100vh-14rem)] min-h-[520px]">

            {{-- Sticky header --}}
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                <p class="text-base font-semibold text-gray-800">Edit device</p>
                <button onclick="closeDeviceEditPanel()"
                        class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
            </div>

            <div id="device-panel-error" class="hidden text-sm text-red-600 bg-red-50 border border-red-200 rounded-md px-3 py-2"></div>

            {{-- Scrollable form body --}}
            <div class="overflow-y-auto flex-1 flex flex-col gap-3 pr-1">

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-500">Name</label>
                    <input type="text" id="device-panel-name"
                           class="text-base border border-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-green-700">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-500">Type</label>
                    <select id="device-panel-type"
                            class="text-base border border-gray-300 rounded-md px-3 py-1.5 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
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
                            class="text-base border border-gray-300 rounded-md px-3 py-1.5 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-500">Model number</label>
                    <input type="text" id="device-panel-model"
                           class="text-base border border-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-green-700">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-500">Serial number</label>
                    <input type="text" id="device-panel-serial"
                           class="text-base border border-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-green-700">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-500">Inventory number</label>
                    <input type="text" id="device-panel-inventory"
                           class="text-base border border-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-green-700">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-500">Specs</label>
                    <textarea id="device-panel-specs" rows="2"
                              class="text-base border border-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-green-700"></textarea>
                </div>

                {{-- Sub-parts toggle --}}
                <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                    <input type="checkbox" id="device-panel-subparts" onchange="togglePanelSubparts()"
                           class="w-4 h-4 accent-green-700">
                    <label for="device-panel-subparts" class="text-sm font-medium text-gray-700">Has sub-parts</label>
                </div>

                {{-- Sub-parts dynamic editor --}}
                <div id="device-panel-subparts-section" class="hidden flex flex-col gap-2 pt-2 border-t border-gray-100">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-semibold text-gray-700">Sub-parts</label>
                        <button type="button" onclick="addPartRowToPanel()"
                                class="px-3 py-1 text-sm font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                            + Add part
                        </button>
                    </div>
                    <div id="device-panel-parts-list" class="flex flex-col gap-2"></div>
                </div>

            </div>

            {{-- Sticky footer --}}
            <div class="pt-2 border-t border-gray-100 flex items-center gap-2">
                <button onclick="saveDeviceEdit()"
                        class="flex-1 px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                    Save
                </button>
                <span id="device-panel-status-msg" class="text-sm text-gray-500 font-medium"></span>
            </div>

        </div>
    </div>

</div>

{{-- Device Details Modal --}}
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

        {{-- Details scrollable body --}}
        <div class="px-6 py-4 flex flex-col gap-3 max-h-96 overflow-y-auto">
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
                <span id="modal-specs" class="text-base text-gray-700 whitespace-pre-wrap"></span>
            </div>
            <div class="flex gap-2">
                <span class="text-base text-gray-400 w-28 shrink-0">Room</span>
                <span id="modal-room" class="text-base text-gray-700"></span>
            </div>

            {{-- Parts --}}
            <div id="modal-parts-section" class="hidden pt-3 border-t border-gray-100">
                <p class="text-base font-semibold text-gray-700 mb-2">Sub-parts</p>
                <div id="modal-parts" class="flex flex-col gap-1"></div>
            </div>

            {{-- Recent Update History --}}
            <div id="modal-logs-section" class="hidden pt-3 border-t border-gray-100">
                <p class="text-base font-semibold text-gray-700 mb-2">Update History</p>
                <div id="modal-logs-list" class="flex flex-col gap-1.5 text-sm"></div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2 bg-gray-50">
            <a id="modal-maintenance-link" href="#"
               class="px-4 py-2 text-base font-medium border border-gray-300 text-gray-700 rounded-md hover:bg-gray-100 transition no-underline">
                Log Maintenance
            </a>
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
    body.dragging { cursor: grabbing !important; user-select: none; }
    body.device-edit-active .room-device { cursor: grab; }
    body.device-edit-active .room-device > div:first-child {
        outline: 2px solid #2563eb;
        outline-offset: 2px;
    }
</style>

{{-- Device data passed to JS --}}
<script>
    const devices            = @json($room->devices->load('status', 'room', 'parts.status'));
    const allStatuses        = @json($statuses);
    const editBaseUrl        = "{{ url('/devices') }}";
    const maintenanceBaseUrl = "{{ url('/maintenance/create') }}";
    const devicePositionBase = "{{ url('/devices') }}";
    const csrfToken          = "{{ csrf_token() }}";

    const statusColors = {
        green:  { bg: '#dcfce7', text: '#15803d' },
        orange: { bg: '#fef9c3', text: '#a16207' },
        red:    { bg: '#fee2e2', text: '#b91c1c' },
    };

    let deviceEditMode  = false;
    let deviceDragState = null;
    let editingDeviceId = null;
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

    function handleDeviceClick(deviceId) {
        if (suppressNextDeviceClick) {
            suppressNextDeviceClick = false;
            return;
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

        // Sub-parts setup
        const hasSubparts = !!device.sub_parts;
        document.getElementById('device-panel-subparts').checked = hasSubparts;
        document.getElementById('device-panel-subparts-section').classList.toggle('hidden', !hasSubparts);

        renderSubpartsInPanel(device.parts || []);

        document.getElementById('device-panel-column').classList.remove('hidden');
        document.getElementById('device-panel-column').classList.add('flex');
    }

    function togglePanelSubparts() {
        const checked = document.getElementById('device-panel-subparts').checked;
        document.getElementById('device-panel-subparts-section').classList.toggle('hidden', !checked);
    }

    function renderSubpartsInPanel(parts) {
        const list = document.getElementById('device-panel-parts-list');
        list.innerHTML = '';
        parts.forEach(p => addPartRowToPanel(p));
    }

    function addPartRowToPanel(part = null) {
        const list = document.getElementById('device-panel-parts-list');
        const partId = part?.id || '';
        const name   = part?.name || '';
        const model  = part?.model_num || '';
        const serial = part?.serial_number || '';
        const inv    = part?.inventory_number || '';
        const statusId = part?.status_id || part?.status?.id || (allStatuses[0]?.id ?? '');

        const statusOpts = allStatuses.map(s =>
            `<option value="${s.id}" ${s.id == statusId ? 'selected' : ''}>${s.label}</option>`
        ).join('');

        const row = document.createElement('div');
        row.className = 'panel-part-row bg-gray-50 border border-gray-200 rounded-lg p-3 flex flex-col gap-2 relative';
        row.dataset.partId = partId;

        row.innerHTML = `
            <div class="flex items-center justify-between pb-1 border-b border-gray-200">
                <span class="font-medium text-gray-700 text-sm">Sub-part</span>
                <button type="button" onclick="this.closest('.panel-part-row').remove()"
                        class="text-red-500 hover:text-red-700 text-sm font-medium">✕ Remove</button>
            </div>
            <div class="flex flex-col gap-1">
                <input type="text" placeholder="Part name *" value="${escapeHtml(name)}"
                       class="part-name-input text-base border border-gray-300 rounded-md px-3 py-1.5 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>
            <div class="flex flex-col gap-1">
                <input type="text" placeholder="Model" value="${escapeHtml(model)}"
                       class="part-model-input text-base border border-gray-300 rounded-md px-3 py-1.5 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>
            <div class="flex flex-col gap-1">
                <select class="part-status-select text-base border border-gray-300 rounded-md px-3 py-1.5 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                    ${statusOpts}
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <input type="text" placeholder="Serial no." value="${escapeHtml(serial)}"
                       class="part-serial-input text-base border border-gray-300 rounded-md px-3 py-1.5 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>
            <div class="flex flex-col gap-1">
                <input type="text" placeholder="Inv no." value="${escapeHtml(inv)}"
                       class="part-inv-input text-base border border-gray-300 rounded-md px-3 py-1.5 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
            </div>
        `;
        list.appendChild(row);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#039;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function closeDeviceEditPanel() {
        editingDeviceId = null;
        document.getElementById('device-panel-column').classList.add('hidden');
        document.getElementById('device-panel-column').classList.remove('flex');
    }

    async function saveDeviceEdit() {
        if (editingDeviceId === null) return;

        const errorBox  = document.getElementById('device-panel-error');
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

        const hasSubparts = document.getElementById('device-panel-subparts').checked;
        const partRows    = document.querySelectorAll('.panel-part-row');
        const partsPayload = [];

        if (hasSubparts) {
            for (const row of partRows) {
                const pName = row.querySelector('.part-name-input').value.trim();
                if (!pName) {
                    statusMsg.textContent = '';
                    errorBox.textContent = 'All sub-parts must have a name.';
                    errorBox.classList.remove('hidden');
                    return;
                }
                partsPayload.push({
                    id:               row.dataset.partId ? parseInt(row.dataset.partId) : null,
                    name:             pName,
                    model_num:        row.querySelector('.part-model-input').value.trim(),
                    serial_number:    row.querySelector('.part-serial-input').value.trim(),
                    inventory_number: row.querySelector('.part-inv-input').value.trim(),
                    status_id:        parseInt(row.querySelector('.part-status-select').value),
                });
            }
        }

        const payload = {
            _method:          'PUT',
            name:             name,
            type:             document.getElementById('device-panel-type').value,
            status_id:        document.getElementById('device-panel-status').value,
            model_num:        document.getElementById('device-panel-model').value,
            serial_number:    document.getElementById('device-panel-serial').value,
            inventory_number: document.getElementById('device-panel-inventory').value,
            specs:            document.getElementById('device-panel-specs').value,
            sub_parts:        hasSubparts ? 1 : 0,
            parts:            partsPayload,
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

        // Update in-memory devices array
        const idx = devices.findIndex(d => d.id === editingDeviceId);
        if (idx !== -1) {
            devices[idx] = { ...devices[idx], ...data };
        }

        // Update the map dot color + tooltip
        const el = document.querySelector(`.room-device[data-id="${editingDeviceId}"]`);
        if (el) {
            const colorMap = { green: '#16a34a', orange: '#facc15', red: '#ef4444' };
            const dot = el.querySelector('div');
            if (dot) dot.style.backgroundColor = colorMap[data.status.color] || '#9ca3af';
            const label = el.querySelector('span');
            if (label) label.textContent = data.name;
        }

        // Update side panel device list row
        const row = document.querySelector(`.device-row[data-id="${editingDeviceId}"]`);
        if (row) {
            const textColorMap = { green: '#15803d', orange: '#a16207', red: '#b91c1c' };
            const bgColorMap   = { green: '#dcfce7', orange: '#fef9c3', red: '#fee2e2' };
            const nameEl = row.querySelector('.device-name');
            if (nameEl) nameEl.textContent = data.name;
            const statusEl = row.querySelector('.device-status-label');
            if (statusEl) {
                statusEl.textContent = data.status.label;
                statusEl.style.color = textColorMap[data.status.color] || '#6b7280';
                statusEl.style.backgroundColor = bgColorMap[data.status.color] || '#f3f4f6';
            }
        }

        statusMsg.textContent = 'Saved ✓';
        setTimeout(() => statusMsg.textContent = '', 3000);
    }

    async function saveAllDevicePositions() {
        const status = document.getElementById('device-save-status');
        status.textContent = 'Saving...';

        const elements = document.querySelectorAll('.room-device');
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

    // ── Positioning (object-contain aware) ──

    function positionDevicesInRoom() {
        const wrapper = document.getElementById('room-map-wrapper');
        const img     = document.getElementById('room-map-img');
        if (!img) return;

        const cRect = wrapper.getBoundingClientRect();
        const cW = cRect.width;
        const cH = cRect.height;
        const iW = img.naturalWidth;
        const iH = img.naturalHeight;

        if (!iW || !iH) return;

        const scale     = Math.min(cW / iW, cH / iH);
        const renderedW = iW * scale;
        const renderedH = iH * scale;
        const offsetX   = (cW - renderedW) / 2;
        const offsetY   = (cH - renderedH) / 2;

        document.querySelectorAll('.room-device').forEach(el => {
            const x = parseFloat(el.dataset.posX);
            const y = parseFloat(el.dataset.posY);

            const pixelX = offsetX + (x / 100) * renderedW;
            const pixelY = offsetY + (y / 100) * renderedH;

            el.style.left      = pixelX + 'px';
            el.style.top       = pixelY + 'px';
            el.style.transform = 'translate(-50%, -50%)';
        });
    }

    const roomMapImg = document.getElementById('room-map-img');
    function initializeRoomMap() {
        requestAnimationFrame(() => {
            positionDevicesInRoom();
        });
    }

    if (roomMapImg) {
        if (roomMapImg.complete) {
            initializeRoomMap();
        } else {
            roomMapImg.addEventListener('load', initializeRoomMap);
        }
    }

    const roomMapWrapper = document.getElementById('room-map-wrapper');
    new ResizeObserver(() => {
        positionDevicesInRoom();
    }).observe(roomMapWrapper);

    window.addEventListener('resize', positionDevicesInRoom);

    // ── Drag to move ─────────────────────────────────────────

    document.addEventListener('mousedown', e => {
        if (!deviceEditMode) return;

        const deviceEl = e.target.closest('.room-device');
        if (!deviceEl) return;

        const wrapper = document.getElementById('room-map-wrapper');
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
    });

    document.addEventListener('mousemove', e => {
        if (!deviceDragState) return;

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
        positionDevicesInRoom();
    });

    document.addEventListener('mouseup', () => {
        if (deviceDragState && deviceDragState.started) {
            suppressNextDeviceClick = true;
        }
        deviceDragState = null;
        document.body.classList.remove('dragging');
    });

    // ── Device modal ─────────────────────────────────────────

    let currentModalDeviceId = null;

    async function openModal(deviceId) {
        const device = devices.find(d => d.id === deviceId);
        if (!device) return;

        currentModalDeviceId = deviceId;

        document.getElementById('modal-name').textContent      = device.name;
        document.getElementById('modal-type').textContent      = device.type;
        document.getElementById('modal-serial').textContent    = device.serial_number || '—';
        document.getElementById('modal-inventory').textContent = device.inventory_number || '—';
        document.getElementById('modal-model').textContent     = device.model_num || '—';
        document.getElementById('modal-specs').textContent     = device.specs || '—';
        document.getElementById('modal-room').textContent      = device.room ? device.room.name : 'Standalone';

        const color  = device.status?.color || 'default';
        const colors = statusColors[color] || { bg: '#f3f4f6', text: '#6b7280' };
        const badge  = document.getElementById('modal-status');
        badge.textContent      = device.status?.label || '—';
        badge.style.background = colors.bg;
        badge.style.color      = colors.text;

        const maintLink = document.getElementById('modal-maintenance-link');
        if (maintLink) {
            maintLink.href = `${maintenanceBaseUrl}/${device.id}`;
        }

        const partsSection = document.getElementById('modal-parts-section');
        const partsList    = document.getElementById('modal-parts');

        if (device.sub_parts && device.parts && device.parts.length > 0) {
            partsList.innerHTML = device.parts.map(part => {
                const pc = statusColors[part.status?.color] || { bg: '#f3f4f6', text: '#6b7280' };
                return `
                    <div class="flex justify-between items-center py-1.5 border-b border-gray-100 text-sm">
                        <div>
                            <span class="text-gray-800 font-medium">${part.name}</span>
                            ${part.model_num ? `<span class="text-gray-400 text-xs ml-2">${part.model_num}</span>` : ''}
                            ${part.serial_number ? `<span class="text-gray-400 text-xs ml-2">SN: ${part.serial_number}</span>` : ''}
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium"
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

        // Fetch and show update logs in modal
        const logsSection = document.getElementById('modal-logs-section');
        const logsList    = document.getElementById('modal-logs-list');
        logsSection.classList.add('hidden');

        try {
            const res = await fetch(`{{ url('/devices') }}/${deviceId}/logs`);
            if (res.ok) {
                const logs = await res.json();
                if (logs && logs.length > 0) {
                    logsList.innerHTML = logs.slice(0, 5).map(l => `
                        <div class="py-1 border-b border-gray-100 flex flex-col gap-0.5">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>${l.action.replace('_', ' ')} by ${l.user ? l.user.username : 'system'}</span>
                                <span>${new Date(l.created_at).toLocaleDateString()}</span>
                            </div>
                            <span class="text-gray-700 text-xs">${l.description}</span>
                        </div>
                    `).join('');
                    logsSection.classList.remove('hidden');
                }
            }
        } catch (e) {
            // Ignore if logs cannot be loaded
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
