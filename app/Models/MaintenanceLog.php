<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    protected $table = 'maintenance_logs';

    protected $fillable = [
        'device_id',
        'performed_by',
        'date',
        'deadline',
        'description',
        'status_before_id',
        'status_after_id',
    ];
    
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function statusBefore()
    {
        return $this->belongsTo(DeviceStatus::class, 'status_before_id');
    }

    public function statusAfter()
    {
        return $this->belongsTo(DeviceStatus::class, 'status_after_id');
    }
}
