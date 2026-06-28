<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $room = Room::create([
            'name'   => $request->name,
            'pos_x'  => 40,
            'pos_y'  => 40,
            'width'  => 15,
            'height' => 15,
            'image'  => null,
        ]);

        return response()->json($room);
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name'   => 'nullable|string|max:255',
            'pos_x'  => 'nullable|numeric',
            'pos_y'  => 'nullable|numeric',
            'width'  => 'nullable|numeric',
            'height' => 'nullable|numeric',
        ]);

        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/rooms'), $filename);
            $room->image = $filename;
            $room->save();
        }

        $room->update([
            'name'   => $request->name   ?? $room->name,
            'pos_x'  => $request->pos_x  ?? $room->pos_x,
            'pos_y'  => $request->pos_y  ?? $room->pos_y,
            'width'  => $request->width  ?? $room->width,
            'height' => $request->height ?? $room->height,
        ]);

        return response()->json($room->fresh());
    }

    public function destroy(Room $room)
    {
        if ($room->devices()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete — this room has ' . $room->devices()->count() . ' device(s) assigned to it.'
            ], 422);
        }

        $room->delete();
        return response()->json(['success' => true]);
    }
}
