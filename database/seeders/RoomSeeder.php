<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $frontDesk     = Room::firstOrCreate(['name' => 'Front Desk'],        ['pos_x' => 21.66, 'pos_y' => 39.44, 'width' => 45.28,  'height' => 6.99, 'image' => 'front-desk.png']);
        $contributions = Room::firstOrCreate(['name' => 'Contributions'],   ['pos_x' => 21.66, 'pos_y' => 29.26, 'width' => 45.28,  'height' => 7.33, 'image' => 'contri.png']);
        $cashier       = Room::firstOrCreate(['name' => 'Cashier'],         ['pos_x' => 0.76,  'pos_y' => 30.39,  'width' => 16.42, 'height' => 16.23, 'image' => 'cashier.png']);
        $lhioHead      = Room::firstOrCreate(['name' => 'LHIO Head'],       ['pos_x' => 72.23, 'pos_y' => 43.54, 'width' => 12.80, 'height' => 19.73, 'image' => 'lhio.png']);
        $wellness      = Room::firstOrCreate(['name' => 'Wellness Area'],   ['pos_x' => 72.18, 'pos_y' => 0.97,  'width' => 19.63, 'height' => 20.13, 'image' => 'wellness-area.png']);
        $membership    = Room::firstOrCreate(['name' => 'Membership'],      ['pos_x' => 56.98, 'pos_y' => 1.01,  'width' => 8.62,  'height' => 20.20, 'image' => 'member-contri.png']);
        $claims        = Room::firstOrCreate(['name' => 'IT and Claims'],   ['pos_x' => 46.94, 'pos_y' => 1.01,  'width' => 8.68,  'height' => 20.20, 'image' => 'claim-admin.png']);
        $itRoom        = Room::firstOrCreate(['name' => 'Server Room'],     ['pos_x' => 33.27, 'pos_y' => 0.80,  'width' => 9.02,  'height' => 9.33,  'image' => 'server.png']);
        $admin         = Room::firstOrCreate(['name' => 'Admin'],           ['pos_x' => 33.26, 'pos_y' => 11.89, 'width' => 4.65,  'height' => 9.31,  'image' => 'admin.png']);
        $pantry        = Room::firstOrCreate(['name' => 'Pantry'],          ['pos_x' => 13.348, 'pos_y' => 0.833, 'width' => 18.812, 'height' => 19.615, 'image' => 'pantry.png']);
        $conference    = Room::firstOrCreate(['name' => 'Conference Room'], ['pos_x' => 86.173, 'pos_y' => 43.269, 'width' => 12.9,  'height' => 41,     'image' => 'conference.png']);
    }
}
