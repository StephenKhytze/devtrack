<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\DeviceStatus;
use App\Models\Device;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    /**
     * List the storage rooms (Local Office Storage, Regional Office Storage)
     * with detailed device and status counts.
     */
    public function index()
    {
        $storageRooms = Room::where('is_storage', true)
            ->withCount('devices')
            ->with(['devices' => function ($query) {
                $query->with('status');
            }])
            ->orderBy('name')
            ->get();

        $totalStoredDevices = Device::whereHas('room', fn($q) => $q->where('is_storage', true))->count();

        return view('storage.index', compact('storageRooms', 'totalStoredDevices'));
    }

    /**
     * Show the device dump for a single storage room with search, filtering,
     * status management, and relocation capabilities.
     */
    public function show(Room $room, Request $request)
    {
        abort_unless($room->is_storage, 404);

        $statuses          = DeviceStatus::all();
        $activeRooms       = Room::where('is_storage', false)->orderBy('name')->get();
        $otherStorageRooms = Room::where('is_storage', true)->where('id', '!=', $room->id)->orderBy('name')->get();
        $sort              = $request->get('sort', 'recent');

        $query = $room->devices()->with(['status', 'room', 'parts.status']);

        if ($request->filled('status')) {
            $query->where('status_id', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
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

        match ($sort) {
            'alpha'  => $query->orderBy('name'),
            'type'   => $query->orderBy('type'),
            'status' => $query->join('device_statuses', 'devices.status_id', '=', 'device_statuses.id')
                              ->orderBy('device_statuses.label')
                              ->select('devices.*'),
            default  => $query->orderBy('devices.created_at', 'desc'),
        };

        $devices = $query->paginate(20)->withQueryString();

        // Calculate counts for this storage room
        $counts = [
            'total'       => $room->devices()->count(),
            'good'        => $room->devices()->whereHas('status', fn($q) => $q->where('color', 'green'))->count(),
            'maintenance' => $room->devices()->whereHas('status', fn($q) => $q->where('color', 'orange'))->count(),
            'oos'         => $room->devices()->whereHas('status', fn($q) => $q->where('color', 'red'))->count(),
        ];

        return view('storage.show', compact('room', 'devices', 'statuses', 'activeRooms', 'otherStorageRooms', 'counts', 'sort'));
    }
}
