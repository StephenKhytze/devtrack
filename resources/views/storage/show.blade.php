@extends('layouts.app')

@section('title', $room->name)

@section('toolbar')
    <a href="{{ route('storage.index') }}"
        class="flex items-center gap-2 text-base text-gray-500 hover:text-green-700 transition">
        ← Back to storage
    </a>


    {{-- Status breakdown matching Floor layout format --}}
    <div class="flex items-center gap-5 ml-4">
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
        <a href="{{ route('devices.create', ['room_id' => $room->id]) }}"
            class="px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition ml-auto">
            + Add device
        </a>
    @endif
@endsection

@section('content')
    <div class="mt-4 flex flex-col gap-4">

        {{-- Search, Filters, and Sorting --}}
        <div class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col gap-3">
            <form method="GET" action="{{ route('storage.show', $room->id) }}" class="flex items-center gap-3 flex-wrap">

                {{-- Search --}}
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search stored devices..."
                    class="text-base border border-gray-300 rounded-md px-3 py-2 w-64 focus:outline-none focus:ring-1 focus:ring-green-700">

                {{-- Status filter --}}
                <select name="status"
                    class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-green-700">
                    <option value="">All statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                            {{ $status->label }}
                        </option>
                    @endforeach
                </select>

                {{-- Type filter --}}
                <select name="type"
                    class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-green-700">
                    <option value="">All types</option>
                    @foreach (['desktop', 'laptop', 'printer', 'photocopier', 'telephone', 'aircon', 'appliance', 'network', 'monitor', 'other'] as $t)
                        <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>
                            {{ ucfirst($t) }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                    class="px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                    Filter
                </button>

                @if (request('search') || request('status') || request('type'))
                    <a href="{{ route('storage.show', $room->id) }}"
                        class="px-4 py-2 text-base font-medium text-gray-500 border border-gray-300 rounded-md hover:bg-gray-100 transition">
                        Clear
                    </a>
                @endif
            </form>

            {{-- Sort Controls --}}
            <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                <span class="text-sm text-gray-500">Sort by:</span>
                <div class="flex border border-gray-300 rounded-md overflow-hidden text-sm">
                    @foreach ([
            'recent' => 'Recent',
            'alpha' => 'A–Z',
            'type' => 'Type',
            'status' => 'Status',
        ] as $value => $label)
                        <a href="{{ request()->fullUrlWithQuery(['sort' => $value, 'page' => 1]) }}"
                            class="px-3 py-1.5 font-medium transition
                              {{ $sort === $value ? 'bg-green-700 text-white' : 'text-gray-500 hover:bg-gray-100' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Devices Table --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-x-auto shadow-sm">
            <table class="w-full text-base">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Name</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Type</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Serial No.</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Inventory No.</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600 w-36">Model</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600 w-44">Specs</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600 whitespace-nowrap">Status</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600 whitespace-nowrap">Sub-parts</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600 whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($devices as $device)
                        <tr class="hover:bg-gray-50 transition cursor-pointer"
                            onclick="openDeviceModal({{ $device->id }})">
                            <td class="px-4 py-3 font-medium text-gray-800 max-w-56 truncate" title="{{ $device->name }}">
                                {{ $device->name }}
                            </td>
                            <td class="px-4 py-3 capitalize text-gray-600 whitespace-nowrap">
                                {{ $device->type }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                {{ $device->serial_number ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                {{ $device->inventory_number ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 max-w-36 truncate" title="{{ $device->model_num ?? '—' }}">
                                {{ $device->model_num ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 max-w-44 truncate" title="{{ $device->specs ?? '—' }}">
                                {{ $device->specs ?? '—' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-3 py-0.5 rounded-full text-base font-medium inline-block whitespace-nowrap"
                                    style="
                                      background-color: {{ match ($device->status->color) {
                                          'green' => '#dcfce7',
                                          'orange' => '#fef9c3',
                                          'red' => '#fee2e2',
                                          default => '#f3f4f6',
                                      } }};
                                      color: {{ match ($device->status->color) {
                                          'green' => '#15803d',
                                          'orange' => '#a16207',
                                          'red' => '#b91c1c',
                                          default => '#6b7280',
                                      } }};">
                                    {{ $device->status->label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-base text-gray-600 whitespace-nowrap" onclick="event.stopPropagation()">
                                @if ($device->sub_parts && $device->parts->isNotEmpty())
                                    <button onclick="toggleParts({{ $device->id }})"
                                        class="text-base text-green-700 underline hover:text-green-900 whitespace-nowrap cursor-pointer">
                                        Show parts ({{ $device->parts->count() }})
                                    </button>
                                @else
                                    <span class="text-gray-400 text-base">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap" onclick="event.stopPropagation()">
                                <div class="flex items-center gap-2 whitespace-nowrap">
                                    @if (auth()->user()->access_type === 'admin')
                                        <a href="{{ route('devices.edit', $device->id) }}"
                                            class="px-3 py-1 text-base border border-gray-300 text-gray-700 rounded-md hover:bg-gray-100 transition no-underline">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('devices.destroy', $device->id) }}"
                                            onsubmit="return confirm('Are you sure you want to delete {{ $device->name }} from storage?');"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1 text-base border border-red-300 text-red-600 rounded-md hover:bg-red-50 transition">
                                                Delete
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-base">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Sub-parts expand row --}}
                        @if ($device->sub_parts && $device->parts->isNotEmpty())
                            <tr id="parts-{{ $device->id }}" class="hidden bg-gray-50 border-b border-gray-100">
                                <td colspan="9" class="px-6 py-3">
                                    <p class="text-sm font-medium text-gray-500 mb-2">Sub-parts</p>
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="text-left text-gray-400 font-normal">
                                                <th class="pb-1 pr-4">Part name</th>
                                                <th class="pb-1 pr-4">Model</th>
                                                <th class="pb-1 pr-4">Serial number</th>
                                                <th class="pb-1 pr-4">Inventory number</th>
                                                <th class="pb-1 pr-4">Specs</th>
                                                <th class="pb-1">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach ($device->parts as $part)
                                                <tr>
                                                    <td class="py-2 pr-4 font-medium text-gray-700">{{ $part->name }}</td>
                                                    <td class="py-2 pr-4 text-gray-500">{{ $part->model_num ?? '—' }}</td>
                                                    <td class="py-2 pr-4 text-gray-500">{{ $part->serial_number ?? '—' }}</td>
                                                    <td class="py-2 pr-4 text-gray-500">{{ $part->inventory_number ?? '—' }}</td>
                                                    <td class="py-2 pr-4 text-gray-500">{{ $part->specs ?? '—' }}</td>
                                                    <td class="py-2">
                                                        <span class="px-2 py-0.5 rounded-full text-sm font-medium inline-block whitespace-nowrap"
                                                            style="
                                                                background-color: {{
                                                                    match($part->status->color) {
                                                                        'green'  => '#dcfce7',
                                                                        'orange' => '#fef9c3',
                                                                        'red'    => '#fee2e2',
                                                                        default  => '#f3f4f6'
                                                                    }
                                                                }};
                                                                color: {{
                                                                    match($part->status->color) {
                                                                        'green'  => '#15803d',
                                                                        'orange' => '#a16207',
                                                                        'red'    => '#b91c1c',
                                                                        default  => '#6b7280'
                                                                    }
                                                                }};">
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
                            <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                                No devices currently in this storage room.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($devices->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                    {{ $devices->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- Device Details Modal --}}
    <div id="device-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden" onclick="closeModal(event)">

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
                <span id="modal-status" class="px-3 py-1 rounded-full text-sm font-medium"></span>
            </div>

            {{-- Details --}}
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
                    <span class="text-base text-gray-400 w-28 shrink-0">Storage room</span>
                    <span id="modal-room" class="text-base text-gray-700 font-medium"></span>
                </div>

                {{-- Parts --}}
                <div id="modal-parts-section" class="hidden pt-3 border-t border-gray-100">
                    <p class="text-base font-medium text-gray-700 mb-2">Sub-parts</p>
                    <div id="modal-parts" class="flex flex-col gap-1"></div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2 bg-gray-50">
                @if (auth()->user()->access_type === 'admin')
                    <a id="modal-edit-link" href="#"
                        class="px-4 py-2 text-base font-medium border border-gray-300 rounded-md hover:bg-gray-100 transition no-underline text-gray-700">
                        Edit device
                    </a>
                @endif
                <button onclick="closeModalDirect()"
                    class="px-4 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                    Close
                </button>
            </div>

        </div>
    </div>

    <script>
        function toggleParts(deviceId) {
            const row = document.getElementById('parts-' + deviceId);
            if (row) {
                row.classList.toggle('hidden');
            }
        }

        const storageDevices = @json($devices->items());
        const editBaseUrl = "{{ url('/devices') }}";

        const statusColors = {
            green: {
                bg: '#dcfce7',
                text: '#15803d'
            },
            orange: {
                bg: '#fef9c3',
                text: '#a16207'
            },
            red: {
                bg: '#fee2e2',
                text: '#b91c1c'
            },
        };

        function openDeviceModal(deviceId) {
            const device = storageDevices.find(d => d.id === deviceId);
            if (!device) return;

            document.getElementById('modal-name').textContent = device.name;
            document.getElementById('modal-type').textContent = device.type;
            document.getElementById('modal-serial').textContent = device.serial_number || '—';
            document.getElementById('modal-inventory').textContent = device.inventory_number || '—';
            document.getElementById('modal-model').textContent = device.model_num || '—';
            document.getElementById('modal-specs').textContent = device.specs || '—';
            document.getElementById('modal-room').textContent = device.room ? device.room.name : '—';

            const color = device.status?.color || 'default';
            const colors = statusColors[color] || {
                bg: '#f3f4f6',
                text: '#6b7280'
            };
            const badge = document.getElementById('modal-status');
            badge.textContent = device.status?.label || '—';
            badge.style.background = colors.bg;
            badge.style.color = colors.text;

            const editLink = document.getElementById('modal-edit-link');
            if (editLink) {
                editLink.href = `${editBaseUrl}/${device.id}/edit`;
            }

            const partsSection = document.getElementById('modal-parts-section');
            const partsList = document.getElementById('modal-parts');

            if (device.sub_parts && device.parts && device.parts.length > 0) {
                partsList.innerHTML = device.parts.map(part => {
                    const pc = statusColors[part.status?.color] || {
                        bg: '#f3f4f6',
                        text: '#6b7280'
                    };
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
    </script>
@endsection
