<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceStatus;
use App\Models\MaintenanceLog;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->get('range', 'all');

        // Device status counts — always current
        $statuses = DeviceStatus::withCount('devices')->get();
        $total    = Device::count();

        // Device type counts — always current
        $typeCounts = Device::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        // Room breakdown — always current
        $rooms = Room::with(['devices.status'])->get();

        // Maintenance logs — filtered by date range
        $logsQuery = MaintenanceLog::with(['device.room', 'statusBefore', 'statusAfter', 'performedBy'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($range === '1month') {
            $logsQuery->whereDate('date', '>=', Carbon::now()->subMonth());
        } elseif ($range === '3months') {
            $logsQuery->whereDate('date', '>=', Carbon::now()->subMonths(3));
        } elseif ($range === '6months') {
            $logsQuery->whereDate('date', '>=', Carbon::now()->subMonths(6));
        }

        $logs = $logsQuery->take(10)->get();

        return view('dashboard', compact(
            'statuses', 'total', 'typeCounts', 'rooms', 'logs', 'range'
        ));
    }

    public function report(Request $request)
    {
        $range = $request->get('range', 'all');

        $statuses   = DeviceStatus::withCount('devices')->get();
        $total      = Device::count();
        $typeCounts = Device::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');
        $rooms = Room::with(['devices.status'])->get();

        $logsQuery = MaintenanceLog::with(['device.room', 'statusBefore', 'statusAfter', 'performedBy'])
            ->orderBy('date', 'desc');

        if ($range === '1month') {
            $logsQuery->whereDate('date', '>=', Carbon::now()->subMonth());
        } elseif ($range === '3months') {
            $logsQuery->whereDate('date', '>=', Carbon::now()->subMonths(3));
        } elseif ($range === '6months') {
            $logsQuery->whereDate('date', '>=', Carbon::now()->subMonths(6));
        }

        $logs = $logsQuery->take(10)->get();

        $generatedAt = Carbon::now()->format('F d, Y h:i A');

        return view('report', compact(
            'statuses', 'total', 'typeCounts', 'rooms', 'logs', 'range', 'generatedAt'
        ));
    }
}
