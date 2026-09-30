<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DeviceUpdateLog extends Model
{
    protected $table = 'device_update_logs';

    protected $fillable = [
        'device_id',
        'device_name',
        'user_id',
        'action',
        'description',
        'changes',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Record a device update log event.
     *
     * @param Device|string $device
     * @param string $action
     * @param string $description
     * @param array|null $changes
     * @param User|null $user
     * @return self
     */
    public static function record($device, string $action, string $description, ?array $changes = null, ?User $user = null): self
    {
        $deviceId   = null;
        $deviceName = 'Unknown Device';

        if ($device instanceof Device) {
            $deviceId   = $device->id;
            $deviceName = $device->name;
        } elseif (is_string($device)) {
            $deviceName = $device;
        }

        $userId = $user ? $user->id : (Auth::id() ?? null);

        return self::create([
            'device_id'   => $deviceId,
            'device_name' => $deviceName,
            'user_id'     => $userId,
            'action'      => $action,
            'description' => $description,
            'changes'     => $changes,
        ]);
    }
}
