<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Room;
use App\Models\DeviceStatus;
use App\Models\DevicePart;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $rooms    = Room::orderBy('name')->get();
        $statuses = DeviceStatus::all();
        $sort     = $request->get('sort', 'recent');

        $query = Device::with(['status', 'room', 'parts.status']);

        if ($request->filled('room')) {
            if ($request->room === 'standalone') {
                $query->whereNull('room_id');
            } else {
                $query->where('room_id', $request->room);
            }
        }

        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('model_num', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%")
                ->orWhere('inventory_number', 'like', "%{$search}%")
                ->orWhere('specs', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%");
            });
        }

        match($sort) {
            'alpha'  => $query->orderBy('name'),
            'room'   => $query->join('rooms', 'devices.room_id', '=', 'rooms.id')
                            ->orderBy('rooms.name')
                            ->select('devices.*'),
            'type'   => $query->orderBy('type'),
            default  => $query->orderBy('devices.created_at', 'desc'),
        };

        $devices = $query->paginate(15)->withQueryString();

        return view('devices.index', compact('devices', 'rooms', 'statuses', 'sort'));
    }

    public function create()
    {
        $rooms    = Room::orderBy('name')->get();
        $statuses = DeviceStatus::all();

        return view('devices.create', compact('rooms', 'statuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                    => 'required|string|max:255',
            'type'                    => 'required|in:desktop,printer,photocopier,telephone,aircon,appliance,network,monitor,laptop,other',
            'model_num'               => 'nullable|string|max:255',
            'serial_number'    => 'nullable|string|max:255',
            'inventory_number' => 'nullable|string|max:255',
            'specs'                   => 'nullable|string',
            'status_id'               => 'required|exists:device_statuses,id',
            'room_id'                 => 'nullable|exists:rooms,id',
            'pos_x'                   => 'nullable|numeric',
            'pos_y'                   => 'nullable|numeric',
            'sub_parts'               => 'boolean',
            'parts'                   => 'nullable|array',
            'parts.*.name'            => 'required_with:parts|string|max:255',
            'parts.*.model_num'       => 'nullable|string|max:255',
            'parts.*.inventory_number' => 'nullable|string|max:255',
            'parts.*.serial_number'    => 'nullable|string|max:255',
            'parts.*.status_id'       => 'required_with:parts|exists:device_statuses,id',
        ]);

        $device = Device::create($request->only([
            'name', 'type', 'model_num', 'serial_number', 'inventory_number', 'specs',
            'status_id', 'room_id', 'pos_x', 'pos_y', 'sub_parts',
        ]));

        if ($request->filled('parts') && $request->boolean('sub_parts')) {
            foreach ($request->parts as $part) {
                $device->parts()->create($part);
            }
        }

        return redirect()->route('devices.index')
                        ->with('success', 'Device added successfully.');
    }

    public function edit(Device $device)
    {
        $rooms    = Room::orderBy('name')->get();
        $statuses = DeviceStatus::all();
        $device->load(['parts.status']);

        return view('devices.edit', compact('device', 'rooms', 'statuses'));
    }

    public function update(Request $request, Device $device)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'type'          => 'required|in:desktop,printer,photocopier,telephone,aircon,appliance,network,monitor,laptop,other',
            'model_num'     => 'nullable|string|max:255',
            'serial_number'    => 'nullable|string|max:255',
            'inventory_number' => 'nullable|string|max:255',
            'specs'         => 'nullable|string',
            'status_id'     => 'required|exists:device_statuses,id',
            'room_id'       => 'nullable|exists:rooms,id',
            'pos_x'         => 'nullable|numeric',
            'pos_y'         => 'nullable|numeric',
            'sub_parts'     => 'boolean',
        ]);

        $device->update($request->only([
            'name', 'type', 'model_num', 'serial_number', 'inventory_number', 'specs',
            'status_id', 'room_id', 'pos_x', 'pos_y', 'sub_parts',
        ]));
        return redirect()->route('devices.index')
                         ->with('success', $device->name . ' updated successfully.');
    }

    /**
     * Quick-edit endpoint used by the Edit Device sidebar on the
     * Floor Layout and Room Layout pages. Deliberately excludes
     * room_id/pos_x/pos_y/sub_parts — those stay on the full edit
     * page and the dedicated position-drag endpoint, so this can't
     * accidentally move a device or touch its sub-parts.
     */
    public function quickUpdate(Request $request, Device $device)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'type'             => 'required|in:desktop,printer,photocopier,telephone,aircon,appliance,network,monitor,laptop,other',
            'model_num'        => 'nullable|string|max:255',
            'serial_number'    => 'nullable|string|max:255',
            'inventory_number' => 'nullable|string|max:255',
            'specs'            => 'nullable|string',
            'status_id'        => 'required|exists:device_statuses,id',
        ]);

        $device->update($validated);
        $device->load('status');

        return response()->json([
            'id'               => $device->id,
            'name'             => $device->name,
            'type'             => $device->type,
            'model_num'        => $device->model_num,
            'serial_number'    => $device->serial_number,
            'inventory_number' => $device->inventory_number,
            'specs'            => $device->specs,
            'status'           => [
                'id'    => $device->status->id,
                'label' => $device->status->label,
                'color' => $device->status->color,
            ],
        ]);
    }

    public function updatePosition(Request $request, Device $device)
    {
        $validated = $request->validate([
            'pos_x' => 'required|numeric|min:0|max:100',
            'pos_y' => 'required|numeric|min:0|max:100',
        ]);

        $device->update($validated);

        return response()->json(['success' => true]);
    }
    public function destroy(Device $device)
    {
        $name = $device->name;
        $device->parts()->delete();
        $device->delete();

        return redirect()->route('devices.index')
                         ->with('success', $name . ' deleted successfully.');
    }
    public function storePart(Request $request, Device $device)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'model_num'        => 'nullable|string|max:255',
            'inventory_number' => 'nullable|string|max:255',
            'serial_number'    => 'nullable|string|max:255',
            'status_id'        => 'required|exists:device_statuses,id',
        ]);

        $device->parts()->create($request->only(['name', 'model_num', 'inventory_number', 'serial_number', 'status_id']));

        return redirect()->route('devices.edit', $device->id)
                        ->with('success', 'Part added successfully.');
    }

    public function updatePart(Request $request, Device $device, DevicePart $part)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'model_num'        => 'nullable|string|max:255',
            'inventory_number' => 'nullable|string|max:255',
            'serial_number'    => 'nullable|string|max:255',
            'status_id'        => 'required|exists:device_statuses,id',
        ]);

        $part->update($request->only(['name', 'model_num', 'inventory_number', 'serial_number', 'status_id']));

        return redirect()->route('devices.edit', $device->id)
                        ->with('success', 'Part updated successfully.');
    }

    public function destroyPart(Device $device, DevicePart $part)
    {
        $part->delete();

        return redirect()->route('devices.edit', $device->id)
                        ->with('success', 'Part deleted successfully.');
    }
}
