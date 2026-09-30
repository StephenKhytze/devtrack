<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Room;
use App\Models\DeviceStatus;
use App\Models\DevicePart;
use App\Models\DeviceUpdateLog;
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
            'name'                     => 'required|string|max:255',
            'type'                     => 'required|in:desktop,printer,photocopier,telephone,aircon,appliance,network,monitor,laptop,other',
            'model_num'                => 'nullable|string|max:255',
            'serial_number'            => 'nullable|string|max:255',
            'inventory_number'         => 'nullable|string|max:255',
            'specs'                    => 'nullable|string',
            'status_id'                => 'required|exists:device_statuses,id',
            'room_id'                  => 'nullable|exists:rooms,id',
            'pos_x'                    => 'nullable|numeric',
            'pos_y'                    => 'nullable|numeric',
            'sub_parts'                => 'boolean',
            'parts'                    => 'nullable|array',
            'parts.*.name'             => 'required_with:parts|string|max:255',
            'parts.*.model_num'        => 'nullable|string|max:255',
            'parts.*.inventory_number' => 'nullable|string|max:255',
            'parts.*.serial_number'    => 'nullable|string|max:255',
            'parts.*.status_id'        => 'required_with:parts|exists:device_statuses,id',
        ]);

        $data = $request->only([
            'name', 'type', 'model_num', 'serial_number', 'inventory_number', 'specs',
            'status_id', 'room_id', 'pos_x', 'pos_y', 'sub_parts',
        ]);

        // If room is storage room, clear position
        if (!empty($data['room_id'])) {
            $targetRoom = Room::find($data['room_id']);
            if ($targetRoom && $targetRoom->is_storage) {
                $data['pos_x'] = null;
                $data['pos_y'] = null;
            }
        }

        $device = Device::create($data);

        if ($request->filled('parts') && $request->boolean('sub_parts')) {
            foreach ($request->parts as $part) {
                $device->parts()->create($part);
            }
        }

        // Record log
        $status = DeviceStatus::find($device->status_id);
        $room   = $device->room_id ? Room::find($device->room_id) : null;
        $roomName = $room ? $room->name : 'Standalone';
        DeviceUpdateLog::record(
            $device,
            'created',
            "Device '{$device->name}' was created with status '{$status?->label}' in {$roomName}.",
            ['initial_data' => $device->toArray()]
        );

        return redirect()->route('devices.index')
                        ->with('success', 'Device added successfully.');
    }

    public function edit(Device $device)
    {
        $rooms    = Room::orderBy('name')->get();
        $statuses = DeviceStatus::all();
        $device->load(['parts.status', 'updateLogs.user']);

        return view('devices.edit', compact('device', 'rooms', 'statuses'));
    }

    public function update(Request $request, Device $device)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'type'             => 'required|in:desktop,printer,photocopier,telephone,aircon,appliance,network,monitor,laptop,other',
            'model_num'        => 'nullable|string|max:255',
            'serial_number'    => 'nullable|string|max:255',
            'inventory_number' => 'nullable|string|max:255',
            'specs'            => 'nullable|string',
            'status_id'        => 'required|exists:device_statuses,id',
            'room_id'          => 'nullable|exists:rooms,id',
            'pos_x'            => 'nullable|numeric',
            'pos_y'            => 'nullable|numeric',
            'sub_parts'        => 'boolean',
        ]);

        $data = $request->only([
            'name', 'type', 'model_num', 'serial_number', 'inventory_number', 'specs',
            'status_id', 'room_id', 'pos_x', 'pos_y', 'sub_parts',
        ]);

        // If target room is storage, null coordinates
        if (!empty($data['room_id'])) {
            $targetRoom = Room::find($data['room_id']);
            if ($targetRoom && $targetRoom->is_storage) {
                $data['pos_x'] = null;
                $data['pos_y'] = null;
            }
        }

        // Detect changed fields for audit log
        $changes = [];
        foreach ($data as $key => $newVal) {
            $oldVal = $device->$key;
            if ($key === 'pos_x' || $key === 'pos_y') {
                if (floatval($oldVal) != floatval($newVal)) {
                    $changes[$key] = ['old' => $oldVal, 'new' => $newVal];
                }
            } elseif ($oldVal != $newVal) {
                $changes[$key] = ['old' => $oldVal, 'new' => $newVal];
            }
        }

        $oldStatusId = $device->status_id;
        $oldRoomId   = $device->room_id;

        $device->update($data);

        // Build descriptive log message
        if (!empty($changes)) {
            $descriptions = [];
            $action = 'updated';

            if (isset($changes['status_id'])) {
                $oldS = DeviceStatus::find($oldStatusId)?->label ?? 'Unknown';
                $newS = DeviceStatus::find($device->status_id)?->label ?? 'Unknown';
                $descriptions[] = "Status changed from '{$oldS}' to '{$newS}'";
                $action = 'status_changed';
            }

            if (isset($changes['room_id'])) {
                $oldR = $oldRoomId ? (Room::find($oldRoomId)?->name ?? 'Unknown') : 'Standalone';
                $newR = $device->room_id ? (Room::find($device->room_id)?->name ?? 'Unknown') : 'Standalone';
                $descriptions[] = "Moved from '{$oldR}' to '{$newR}'";
                $action = 'moved';
            }

            if (empty($descriptions)) {
                $changedFieldNames = implode(', ', array_keys($changes));
                $descriptions[] = "Updated fields: {$changedFieldNames}";
            }

            DeviceUpdateLog::record(
                $device,
                $action,
                "Device '{$device->name}': " . implode('; ', $descriptions),
                $changes
            );
        }

        return redirect()->route('devices.index')
                         ->with('success', $device->name . ' updated successfully.');
    }

    /**
     * Quick-edit endpoint used by the Edit Device sidebar on Floor Layout,
     * Room Layout, and Storage pages. Now includes full sub-parts management.
     */
    public function quickUpdate(Request $request, Device $device)
    {
        $validated = $request->validate([
            'name'                     => 'required|string|max:255',
            'type'                     => 'required|in:desktop,printer,photocopier,telephone,aircon,appliance,network,monitor,laptop,other',
            'model_num'                => 'nullable|string|max:255',
            'serial_number'            => 'nullable|string|max:255',
            'inventory_number'         => 'nullable|string|max:255',
            'specs'                    => 'nullable|string',
            'status_id'                => 'required|exists:device_statuses,id',
            'sub_parts'                => 'nullable|boolean',
            'parts'                    => 'nullable|array',
            'parts.*.id'               => 'nullable|integer',
            'parts.*.name'             => 'required_with:parts|string|max:255',
            'parts.*.model_num'        => 'nullable|string|max:255',
            'parts.*.serial_number'    => 'nullable|string|max:255',
            'parts.*.inventory_number' => 'nullable|string|max:255',
            'parts.*.specs'            => 'nullable|string',
            'parts.*.status_id'        => 'required_with:parts|exists:device_statuses,id',
        ]);

        $deviceData = [
            'name'             => $validated['name'],
            'type'             => $validated['type'],
            'model_num'        => $validated['model_num'] ?? null,
            'serial_number'    => $validated['serial_number'] ?? null,
            'inventory_number' => $validated['inventory_number'] ?? null,
            'specs'            => $validated['specs'] ?? null,
            'status_id'        => $validated['status_id'],
        ];

        if ($request->has('sub_parts')) {
            $deviceData['sub_parts'] = $request->boolean('sub_parts');
        }

        // Track changes for logging
        $changes = [];
        foreach ($deviceData as $k => $val) {
            if ($device->$k != $val) {
                $changes[$k] = ['old' => $device->$k, 'new' => $val];
            }
        }

        $device->update($deviceData);

        // Sync sub parts if sub_parts is true
        if ($device->sub_parts && $request->has('parts')) {
            $incomingParts = $request->input('parts', []);
            $keptPartIds = [];

            foreach ($incomingParts as $pData) {
                if (!empty($pData['id'])) {
                    // Update existing part
                    $part = DevicePart::where('device_id', $device->id)->find($pData['id']);
                    if ($part) {
                        $part->update([
                            'name'             => $pData['name'],
                            'model_num'        => $pData['model_num'] ?? null,
                            'serial_number'    => $pData['serial_number'] ?? null,
                            'inventory_number' => $pData['inventory_number'] ?? null,
                            'specs'            => $pData['specs'] ?? null,
                            'status_id'        => $pData['status_id'],
                        ]);
                        $keptPartIds[] = $part->id;
                    }
                } else {
                    // Create new part
                    $newPart = $device->parts()->create([
                        'name'             => $pData['name'],
                        'model_num'        => $pData['model_num'] ?? null,
                        'serial_number'    => $pData['serial_number'] ?? null,
                        'inventory_number' => $pData['inventory_number'] ?? null,
                        'specs'            => $pData['specs'] ?? null,
                        'status_id'        => $pData['status_id'],
                    ]);
                    $keptPartIds[] = $newPart->id;
                }
            }

            // Remove parts deleted in the form
            $device->parts()->whereNotIn('id', $keptPartIds)->delete();
        }

        // Log the quick update
        $action = isset($changes['status_id']) ? 'status_changed' : 'quick_updated';
        DeviceUpdateLog::record(
            $device,
            $action,
            "Quick updated device '{$device->name}' (including sub-parts).",
            $changes
        );

        $device->load(['status', 'room', 'parts.status']);

        return response()->json($device);
    }

    public function updatePosition(Request $request, Device $device)
    {
        $validated = $request->validate([
            'pos_x' => 'required|numeric|min:0|max:100',
            'pos_y' => 'required|numeric|min:0|max:100',
        ]);

        $oldX = $device->pos_x;
        $oldY = $device->pos_y;

        $device->update($validated);

        DeviceUpdateLog::record(
            $device,
            'moved',
            "Repositioned '{$device->name}' on map from ({$oldX}%, {$oldY}%) to ({$device->pos_x}%, {$device->pos_y}%).",
            ['pos_x' => ['old' => $oldX, 'new' => $device->pos_x], 'pos_y' => ['old' => $oldY, 'new' => $device->pos_y]]
        );

        return response()->json(['success' => true]);
    }

    public function destroy(Device $device)
    {
        $name = $device->name;

        DeviceUpdateLog::record(
            $name,
            'deleted',
            "Device '{$name}' was deleted from the system."
        );

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
            'specs'            => 'nullable|string',
            'status_id'        => 'required|exists:device_statuses,id',
        ]);

        $part = $device->parts()->create($request->only(['name', 'model_num', 'inventory_number', 'serial_number', 'specs', 'status_id']));

        DeviceUpdateLog::record(
            $device,
            'part_added',
            "Added sub-part '{$part->name}' to device '{$device->name}'.",
            ['part' => $part->toArray()]
        );

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
            'specs'            => 'nullable|string',
            'status_id'        => 'required|exists:device_statuses,id',
        ]);

        $part->update($request->only(['name', 'model_num', 'inventory_number', 'serial_number', 'specs', 'status_id']));

        DeviceUpdateLog::record(
            $device,
            'part_updated',
            "Updated sub-part '{$part->name}' for device '{$device->name}'.",
            ['part' => $part->toArray()]
        );

        return redirect()->route('devices.edit', $device->id)
                        ->with('success', 'Part updated successfully.');
    }

    public function destroyPart(Device $device, DevicePart $part)
    {
        $partName = $part->name;
        $part->delete();

        DeviceUpdateLog::record(
            $device,
            'part_deleted',
            "Deleted sub-part '{$partName}' from device '{$device->name}'."
        );

        return redirect()->route('devices.edit', $device->id)
                        ->with('success', 'Part deleted successfully.');
    }
}
