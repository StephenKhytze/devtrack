<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'name',
        'type',
        'model_num',
        'serial_number',
        'specs',
        'sub_parts',
        'status_id',
        'room_id',
        'pos_x',
        'pos_y',
    ];

    public function parts()
    {
        return $this->hasMany(DevicePart::class);
    }

    public function status()
    {
    return $this->belongsTo(DeviceStatus::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
