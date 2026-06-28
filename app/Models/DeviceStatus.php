<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceStatus extends Model
{
    protected $table = 'device_statuses';

    protected $fillable = [
        'label',
        'color',
    ];

    public $timestamps = false;

    public function devices()
    {
        return $this->hasMany(Device::class, 'status_id');
    }
}
