<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use App\Models\ItemLog;
use App\Models\Room;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Ambil ruangan aktif dari session, jika belum ada gunakan room_id default bawaan user
        $activeRoomId = session('active_room_id', $user->room_id);

        $itemsQuery = Item::query();
        $borrowingsQuery = Borrowing::query();
        $logsQuery = ItemLog::with(['user', 'item']);

        // Jika login sebagai Petugas Ruangan, batasi query hanya untuk ruangan aktif saat ini
        if ($user->hasRole('petugas')) {
            $itemsQuery->where('room_id', $activeRoomId);
            
            $borrowingsQuery->where(function($q) use ($activeRoomId) {
                $q->where('from_room_id', $activeRoomId)
                  ->orWhere('to_room_id', $activeRoomId);
            });

            $logsQuery->whereHas('item', function($q) use ($activeRoomId) {
                $q->where('room_id', $activeRoomId);
            });
        }

        $total_items = (clone $itemsQuery)->sum('qty');
        $good_items = (clone $itemsQuery)->where('status', 'Baik')->sum('qty');
        $damaged_items = (clone $itemsQuery)->where('status', 'Rusak')->sum('qty');
        $used_items = $borrowingsQuery->where('status', 'Pending')->count();
        $recent_activities = $logsQuery->latest()->take(5)->get();

        $roomsQuery = Room::withSum('items', 'qty');
        if ($user->hasRole('petugas')) {
            $roomsQuery->where('id', $activeRoomId);
        }
        $rooms_chart = $roomsQuery->get()->map(function($room) {
            $room->total_qty = $room->items_sum_qty ?? 0;
            return $room;
        });

        return view('dashboard.index', compact('total_items', 'good_items', 'damaged_items', 'used_items', 'recent_activities', 'rooms_chart'));
    }

    public function switchRoom(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id'
        ]);

        $room = Room::findOrFail($request->room_id);

        // Amankan agar petugas tidak bisa menembak room_id milik petugas lain
        if (auth()->user()->role->name !== 'Admin' && $room->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke ruangan ini.');
        }

        session(['active_room_id' => $room->id]);

        return redirect()->back()->with('success', 'Berhasil beralih ke ruangan: ' . $room->name);
    }
}