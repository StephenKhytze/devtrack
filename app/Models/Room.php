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
        'is_storage',
    ];

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_storage', false);
    }

    public function scopeStorage($query)
    {
        return $query->where('is_storage', true);
    }
}
