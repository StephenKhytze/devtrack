@extends('layouts.app')

@section('title', $room->name)

@section('toolbar')
    <a href="{{ route('storage.index') }}"
       class="flex items-center gap-2 text-base text-gray-500 hover:text-green-700 transition">
        ← Back to Storage
    </a>
    <span class="text-base font-medium text-gray-800">{{ $room->name }}</span>
@endsection

@section('content')
<div class="mt-4 flex gap-4">

    <div class="flex-1 min-w-0">
        <div class="py-3 text-base text-gray-500">
            Showing <strong class="text-gray-800">{{ $room->devices->count() }}</strong> device(s)
        </div>

        <div class="mb-6 bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full text-base">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-3 py-3 font-medium text-gray-600">Name</th>
                        <th class="text-left px-3 py-3 font-medium text-gray-600">Type</th>
                        <th class="text-left px-3 py-3 font-medium text-gray-600">Serial num.</th>
                        <th class="text-left px-3 py-3 font-medium text-gray-600">Inventory num.</th>
                        <th class="text-left px-3 py-3 font-medium text-gray-600">Model</th>
                        <th class="text-left px-3 py-3 font-medium text-gray-600">Status</th>
                        <th class="text-left px-3 py-3 font-medium text-gray-600">Parts</th>
                        @if (auth()->user()->access_type === 'admin')
                            <th class="text-left px-3 py-3 font-medium text-gray-600">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($room->devices as $device)
                        <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="handleDeviceClick({{ $device->id }})">
                            <td class="px-3 py-3 font-medium text-gray-800 whitespace-nowrap">{{ $device->name }}</td>
                            <td class="px-3 py-3 capitalize text-gray-600 whitespace-nowrap">{{ $device->type }}</td>
                            <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $device->serial_number ?? '—' }}</td>
                            <td class="px-3 py-3 text-gray-600 whitespace-nowrap">{{ $device->inventory_number ?? '—' }}</td>
                            <td class="px-3 py-3 text-gray-600 max-w-36 truncate" title="{{ $device->model_num ?? '—' }}">{{ $device->model_num ?? '—' }}</td>
                            <td class="px-3 py-3">
                                <span class="px-2 py-1 rounded-full text-sm font-medium"
                                    style="
                                        background-color: {{ match($device->status->color) { 'green' => '#dcfce7', 'orange' => '#fef9c3', 'red' => '#fee2e2', default => '#f3f4f6' } }};
                                        color: {{ match($device->status->color) { 'green' => '#15803d', 'orange' => '#a16207', 'red' => '#b91c1c', default => '#6b7280' } }};">
                                    {{ $device->status->label }}
                                </span>
                            </td>
                            <td class="px-3 py-3" onclick="event.stopPropagation()">
                                @if ($device->sub_parts)
                                    <button onclick="toggleParts({{ $device->id }})"
                                            class="text-sm text-green-700 underline hover:text-green-900 whitespace-nowrap">
                                        Show parts ({{ $device->parts->count() }})
                                    </button>
                                @else
                                    <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>
                            @if (auth()->user()->access_type === 'admin')
                                <td class="px-3 py-3" onclick="event.stopPropagation()">
                                    <div class="flex items-center gap-2">
                                        <button onclick="openDeviceEditPanel({{ $device->id }})"
                                                class="px-3 py-1 text-sm font-medium text-green-700 border border-green-700 rounded-md hover:bg-green-50 transition">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('devices.destroy', $device->id) }}"
                                              onsubmit="return confirm('Delete {{ $device->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1 text-sm font-medium text-red-600 border border-red-300 rounded-md hover:bg-red-50 transition">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>

                        @if ($device->sub_parts && $device->parts->isNotEmpty())
                            <tr id="parts-{{ $device->id }}" class="hidden bg-gray-50">
                                <td colspan="8" class="px-8 py-3">
                                    <p class="text-sm font-medium text-gray-500 mb-2">Parts for {{ $device->name }}</p>
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="text-gray-500">
                                                <th class="text-left py-1 pr-4">Part name</th>
                                                <th class="text-left py-1 pr-4">Model</th>
                                                <th class="text-left py-1 pr-4">Serial num.</th>
                                                <th class="text-left py-1 pr-4">Inventory num.</th>
                                                <th class="text-left py-1">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach ($device->parts as $part)
                                                <tr>
                                                    <td class="py-2 pr-4 text-gray-700">{{ $part->name }}</td>
                                                    <td class="py-2 pr-4 text-gray-500">{{ $part->model_num ?? '—' }}</td>
                                                    <td class="py-2 pr-4 text-gray-500">{{ $part->serial_number ?? '—' }}</td>
                                                    <td class="py-2 pr-4 text-gray-500">{{ $part->inventory_number ?? '—' }}</td>
                                                    <td class="py-2">
                                                        <span class="px-2 py-0.5 rounded-full text-sm font-medium"
                                                            style="
                                                                background-color: {{ match($part->status->color) { 'green' => '#dcfce7', 'orange' => '#fef9c3', 'red' => '#fee2e2', default => '#f3f4f6' } }};
                                                                color: {{ match($part->status->color) { 'green' => '#15803d', 'orange' => '#a16207', 'red' => '#b91c1c', default => '#6b7280' } }};">
                                                            {{ $part->status->label }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-8 text-center text-gray-400">
                                No devices in this storage room.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Edit Device panel — same pattern as Floor Layout / Room Layout --}}
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

<script>
    const csrfToken = "{{ csrf_token() }}";
    const devicePositionBase = "{{ url('/devices') }}";
    const devices = @json($room->devices->load('status', 'parts.status'));

    let editingDeviceId = null;

    function toggleParts(deviceId) {
        const row = document.getElementById('parts-' + deviceId);
        row.classList.toggle('hidden');
    }

    function handleDeviceClick(deviceId) {
        // No map/drag concept in Storage — clicking a row just opens the view.
        // Admin edits happen via the explicit Edit button, not the row click.
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

        const payload = {
            name:              name,
            type:              document.getElementById('device-panel-type').value,
            status_id:         document.getElementById('device-panel-status').value,
            model_num:         document.getElementById('device-panel-model').value,
            serial_number:     document.getElementById('device-panel-serial').value,
            inventory_number:  document.getElementById('device-panel-inventory').value,
            specs:             document.getElementById('device-panel-specs').value,
        };

        const res = await fetch(`${devicePositionBase}/${editingDeviceId}/quick-update`, {
            method: 'PUT',
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

        statusMsg.textContent = 'Saved ✓';
        setTimeout(() => location.reload(), 800);
    }
</script>
@endsection
