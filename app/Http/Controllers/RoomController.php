<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('pj')->latest()->get();
        // Mengambil semua user dengan role Petugas Ruangan untuk diplot ke dropdown form
        $users = User::whereHas('role', function($q) {
            $q->where('name', 'petugas');
        })->get();

        return view('rooms.index', compact('rooms', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name',
            'user_id' => 'nullable|exists:users,id',
            'location_floor' => 'required'
        ]);

        Room::create($validated);

        return redirect()->route('rooms.index')->with('success', 'Ruangan baru berhasil ditambahkan.');
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name,' . $room->id,
            'user_id' => 'nullable|exists:users,id',
            'location_floor' => 'required'
        ]);

        $room->update($validated);

        return redirect()->route('rooms.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Ruangan berhasil dihapus.');
    }
}