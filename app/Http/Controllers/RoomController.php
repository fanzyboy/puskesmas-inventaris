<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index() {
        $rooms = Room::with('pj')->get();
        $users = User::where('role_id', 2)->get(); // Hanya ambil user petugas
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

    // --- PASTIKAN METHOD UPDATE INI SUDAH SESUAI ---
    public function update(Request $request, Room $room) {
        $validated = $request->validate([
            'name' => 'required|string|unique:rooms,name,' . $room->id,
            'location_floor' => 'required|string',
            'user_id' => 'nullable|exists:users,id'
        ]);

        // Lepas ruangan dari PJ lama terlebih dahulu jika PJ diubah
        if ($room->user_id && $room->user_id != $request->user_id) {
            User::where('id', $room->user_id)->update(['room_id' => null]);
        }

        $room->update($validated);

        // Pasangkan ruangan ke user petugas yang baru ditunjuk
        if ($request->filled('user_id')) {
            User::where('id', $request->user_id)->update(['room_id' => $room->id]);
        }

        return redirect()->route('rooms.index')->with('success', 'Data tata ruang & Penanggung Jawab berhasil diperbarui.');
    }

    public function destroy(Room $room) {
        // Kosongkan room_id di user terkait sebelum dihapus agar tidak error foreign key
        User::where('room_id', $room->id)->update(['room_id' => null]);
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Ruangan berhasil dihapus.');
    }
}