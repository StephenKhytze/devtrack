<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevicePart extends Model
{
    protected $table = 'device_parts';

    protected $fillable = [
        'device_id',
        'name',
        'model_num',
        'inventory_number',
        'serial_number',
        'specs',
        'status_id',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function status()
    {
        return $this->belongsTo(DeviceStatus::class, 'status_id');
    }
}
