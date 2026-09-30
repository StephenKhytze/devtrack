<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Device;
use App\Models\DeviceStatus;

class FloorController extends Controller
{
    public function index()
    {
        $rooms = Room::where('is_storage', false)
            ->with(['devices' => function ($query) {
                $query->with('status');
            }])->get();

        $standaloneDevices = Device::with(['status', 'parts.status'])
            ->whereNull('room_id')
            ->get();

        $counts = [
            'good'        => Device::whereHas('status', fn($q) => $q->where('color', 'green'))->count(),
            'maintenance' => Device::whereHas('status', fn($q) => $q->where('color', 'orange'))->count(),
            'oos'         => Device::whereHas('status', fn($q) => $q->where('color', 'red'))->count(),
        ];

        $statuses = DeviceStatus::all();

        return view('floor', compact('rooms', 'counts', 'standaloneDevices', 'statuses'));
    }
    public function room(Room $room)
    {
        if ($room->is_storage) {
            return redirect()->route('storage.show', $room->id);
        }

        $room->load(['devices' => function ($query) {
            $query->with(['status', 'parts.status']);
        }]);

        $statuses = DeviceStatus::all();

        return view('floor.room', compact('room', 'statuses'));
    }
}
