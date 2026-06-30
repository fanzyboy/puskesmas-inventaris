<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Requests\StoreItemRequest;

class ItemController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Item::with('room');

        if ($user->hasRole('petugas')) {
            $activeRoomId = session('active_room_id', $user->room_id);
            $query->where('room_id', $activeRoomId);
        }

        $items = $query->paginate(16);
        $rooms = Room::all();
        return view('items.index', compact('items', 'rooms'));
    }

    public function store(StoreItemRequest $request)
    {
        $validated = $request->validated();
        
        // Generate item code if not provided
        $validated['item_code'] = 'INV-' . strtoupper(substr(uniqid(), -6));
        
        Item::create($validated);
        
        return redirect()->route('items.index')->with('success', 'Aset baru berhasil diregistrasi.');
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'qty' => 'required|integer|min:0',
            'status' => 'required|string'
        ]);

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'Status aset berhasil diperbarui.');
    }
}