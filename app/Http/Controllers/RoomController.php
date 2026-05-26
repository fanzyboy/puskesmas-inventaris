<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index() {
        $rooms = Room::with('pj')->get();
        $users = User::where('role_id', 2)->get(); // Hanya ambil user petugas untuk calon PJ
        return view('rooms.index', compact('rooms', 'users'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|unique:rooms,name',
            'location_floor' => 'required|string',
            'user_id' => 'nullable|exists:users,id'
        ]);

        $room = Room::create($validated);
        if ($request->filled('user_id')) {
            User::where('id', $request->user_id)->update(['room_id' => $room->id]);
        }

        return redirect()->route('rooms.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function update(Request $request, Room $room) {
        $validated = $request->validate([
            'name' => 'required|string|unique:rooms,name,' . $room->id,
            'location_floor' => 'required|string',
            'user_id' => 'nullable|exists:users,id'
        ]);

        $room->update($validated);
        if ($request->filled('user_id')) {
            User::where('id', $request->user_id)->update(['room_id' => $room->id]);
        }

        return redirect()->route('rooms.index')->with('success', 'Ruangan berhasil diupdate.');
    }

    public function destroy(Room $room) {
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Ruangan berhasil dihapus.');
    }
}