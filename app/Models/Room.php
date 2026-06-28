<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'name',
        'pos_x',
        'pos_y',
        'width',
        'height',
        'image',
    ];
    public function devices()
    {
        return $this->hasMany(Device::class);
    }
}
