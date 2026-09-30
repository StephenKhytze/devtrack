<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class StorageRoomSeeder extends Seeder
{
    /**
     * Storage rooms are a special kind of Room: they hold devices that
     * are no longer in active use, but they don't appear on the Floor
     * Layout map and have no physical position/size/image — those
     * fields are irrelevant here, so pos_x/pos_y/width/height are just
     * set to 0 as placeholders and never used or displayed.
     */
    public function run(): void
    {
        Room::firstOrCreate(
            ['name' => 'Local Office Storage'],
            [
                'pos_x'      => 0,
                'pos_y'      => 0,
                'width'      => 0,
                'height'     => 0,
                'image'      => null,
                'is_storage' => true,
            ]
        );

        Room::firstOrCreate(
            ['name' => 'Regional Office Storage'],
            [
                'pos_x'      => 0,
                'pos_y'      => 0,
                'width'      => 0,
                'height'     => 0,
                'image'      => null,
                'is_storage' => true,
            ]
        );
    }
}
