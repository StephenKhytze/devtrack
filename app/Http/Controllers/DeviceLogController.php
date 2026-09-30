<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceUpdateLog;
use App\Models\User;
use Illuminate\Http\Request;

class DeviceLogController extends Controller
{
    /**
     * Display a listing of device update logs with filtering and search.
     */
    public function index(Request $request)
    {
        $devices = Device::orderBy('name')->get();
        $users   = User::orderBy('username')->get();
        $sort    = $request->get('sort', 'recent');

        $query = DeviceUpdateLog::with(['device', 'user']);

        if ($request->filled('device_id')) {
            $query->where('device_id', $request->device_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('device_name', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('username', 'like', "%{$search}%"));
            });
        }

        match ($sort) {
            'device' => $query->orderBy('device_name'),
            'action' => $query->orderBy('action'),
            default  => $query->orderBy('created_at', 'desc'),
        };

        $logs = $query->paginate(20)->withQueryString();

        $actions = [
            'created'        => 'Device Created',
            'updated'        => 'Device Updated',
            'quick_updated'  => 'Quick Updated',
            'status_changed' => 'Status Changed',
            'moved'          => 'Location Moved',
            'part_added'     => 'Part Added',
            'part_updated'   => 'Part Updated',
            'part_deleted'   => 'Part Deleted',
            'deleted'        => 'Device Deleted',
            'maintenance'    => 'Maintenance Logged',
        ];

        return view('devices.logs', compact('logs', 'devices', 'users', 'actions', 'sort'));
    }

    /**
     * Return JSON update logs for a specific device.
     */
    public function forDevice(Device $device)
    {
        $logs = $device->updateLogs()->with('user')->take(20)->get();

        return response()->json($logs);
    }
}
