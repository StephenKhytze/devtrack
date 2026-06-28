<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceLog;
use App\Models\Device;
use App\Models\DeviceStatus;
use App\Models\User;
use Illuminate\Http\Request;

class MaintenanceLogController extends Controller
{
    public function index(Request $request)
    {
        $devices  = Device::orderBy('name')->get();
        $statuses = DeviceStatus::all();

        $query = MaintenanceLog::with(['device.room', 'statusBefore', 'statusAfter', 'performedBy'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->filled('device')) {
            $query->where('device_id', $request->device);
        }

        if ($request->filled('status_after')) {
            $query->where('status_after_id', $request->status_after);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                ->orWhereHas('device', fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->orWhereHas('performedBy', fn($q) => $q->where('username', 'like', "%{$search}%"));
            });
        }

        $logs = $query->get();

        return view('maintenance.index', compact('logs', 'devices', 'statuses'));
    }

    public function create()
    {
        $devices  = Device::with('status')->orderBy('name')->get();
        $statuses = DeviceStatus::all();
        $users    = User::all();

        return view('maintenance.create', compact('devices', 'statuses', 'users'));
    }

    public function createForDevice(Device $device)
    {
        $devices  = Device::with('status')->orderBy('name')->get();
        $statuses = DeviceStatus::all();
        $users    = User::all();

        return view('maintenance.create', compact('device', 'devices', 'statuses', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_id'        => 'required|exists:devices,id',
            'performed_by_name'=> 'required|string|max:255',
            'date'             => 'required|date',
            'deadline'         => 'nullable|date|after_or_equal:date',
            'description'      => 'required|string',
            'status_before_id' => 'required|exists:device_statuses,id',
            'status_after_id'  => 'required|exists:device_statuses,id',
        ]);

        // Find existing user or create a new one
        $user = User::firstOrCreate(
            ['username' => $request->performed_by_name],
            ['password' => bcrypt('password'), 'access_type' => 'staff']
        );

        MaintenanceLog::create([
            'device_id'        => $request->device_id,
            'performed_by'     => $user->id,
            'date'             => $request->date,
            'description'      => $request->description,
            'status_before_id' => $request->status_before_id,
            'status_after_id'  => $request->status_after_id,
        ]);

        $device = Device::find($request->device_id);
        $device->status_id = $request->status_after_id;
        $device->save();

        return redirect()->route('maintenance.index')
                        ->with('success', 'Maintenance log added successfully.');
    }

    public function show(MaintenanceLog $log)
    {
        $log->load(['device.room', 'statusBefore', 'statusAfter', 'performedBy']);

        return view('maintenance.show', compact('log'));
    }
}
