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
        $sort     = $request->get('sort', 'recent');

        $query = MaintenanceLog::with(['device.room', 'statusBefore', 'statusAfter', 'performedBy']);

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

        match ($sort) {
            'alpha' => $query->join('devices', 'maintenance_logs.device_id', '=', 'devices.id')
                              ->orderBy('devices.name')
                              ->select('maintenance_logs.*'),
            'room'  => $query->join('devices', 'maintenance_logs.device_id', '=', 'devices.id')
                              ->leftJoin('rooms', 'devices.room_id', '=', 'rooms.id')
                              ->orderBy('rooms.name')
                              ->select('maintenance_logs.*'),
            'type'  => $query->join('devices', 'maintenance_logs.device_id', '=', 'devices.id')
                              ->orderBy('devices.type')
                              ->select('maintenance_logs.*'),
            default => $query->orderBy('date', 'desc')->orderBy('created_at', 'desc'),
        };

        $logs = $query->paginate(10)->withQueryString();

        return view('maintenance.index', compact('logs', 'devices', 'statuses', 'sort'));
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

        \App\Models\DeviceUpdateLog::record(
            $device,
            'maintenance',
            "Maintenance performed by {$user->username}: {$request->description}",
            [
                'status_before_id' => $request->status_before_id,
                'status_after_id'  => $request->status_after_id,
                'date'             => $request->date,
                'deadline'         => $request->deadline,
            ],
            $user
        );

        return redirect()->route('maintenance.index')
                        ->with('success', 'Maintenance log added successfully.');
    }

    public function show(MaintenanceLog $log)
    {
        $log->load(['device.room', 'statusBefore', 'statusAfter', 'performedBy']);

        return view('maintenance.show', compact('log'));
    }
    public function edit(MaintenanceLog $log)
    {
        $devices  = Device::with('status')->orderBy('name')->get();
        $statuses = DeviceStatus::all();
        $log->load(['device', 'statusBefore', 'statusAfter', 'performedBy']);

        return view('maintenance.edit', compact('log', 'devices', 'statuses'));
    }

    public function update(Request $request, MaintenanceLog $log)
    {
        $request->validate([
            'performed_by_name' => 'required|string|max:255',
            'date'              => 'required|date',
            'deadline'          => 'nullable|date|after_or_equal:date',
            'description'       => 'required|string',
            'status_before_id'  => 'required|exists:device_statuses,id',
            'status_after_id'   => 'required|exists:device_statuses,id',
        ]);

        $user = User::firstOrCreate(
            ['username' => $request->performed_by_name],
            ['password' => bcrypt('password'), 'access_type' => 'staff']
        );

        $log->update([
            'performed_by'     => $user->id,
            'date'             => $request->date,
            'deadline'         => $request->deadline,
            'description'      => $request->description,
            'status_before_id' => $request->status_before_id,
            'status_after_id'  => $request->status_after_id,
        ]);

        // Update device status to match status_after
        $log->device->status_id = $request->status_after_id;
        $log->device->save();

        \App\Models\DeviceUpdateLog::record(
            $log->device,
            'maintenance',
            "Maintenance log updated by {$user->username}: {$request->description}",
            [
                'status_before_id' => $request->status_before_id,
                'status_after_id'  => $request->status_after_id,
                'date'             => $request->date,
                'deadline'         => $request->deadline,
            ],
            $user
        );

        return redirect()->route('maintenance.index')
                        ->with('success', 'Maintenance log updated successfully.');
    }
}
