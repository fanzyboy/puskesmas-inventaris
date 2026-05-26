<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Room;
use App\Models\ItemLog;
use App\Http\Requests\StoreItemRequest;
use Illuminate\Http\Request;
use Auth;

class ItemController extends Controller
{
    public function index() {
        $user = Auth::user();
        $rooms = Room::all();
        
        $items = $user->hasRole('admin') 
            ? Item::with('room')->get() 
            : Item::with('room')->where('room_id', $user->room_id)->get();

        return view('items.index', compact('items', 'rooms'));
    }

    public function store(StoreItemRequest $request) {
        $validated = $request->validated();
        $validated['item_code'] = 'INV-' . strtoupper(substr($validated['category'], 0, 3)) . '-' . rand(1000, 9999);

        $item = Item::create($validated);

        ItemLog::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'action' => 'Inisialisasi Barang Baru',
            'new_values' => json_encode($item->toArray())
        ]);

        return redirect()->route('items.index')->with('success', 'Aset Berhasil Didaftarkan.');
    }

    public function update(Request $request, Item $item) {
        if (Auth::user()->hasRole('petugas') && $item->room_id != Auth::user()->room_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:0',
            'status' => 'required|in:Baik,Rusak,Tidak Tersedia,Digunakan'
        ]);

        $oldValues = $item->toArray();
        $item->update($validated);

        ItemLog::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'action' => 'Pembaruan Kondisi Fasilitas',
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($item->fresh()->toArray())
        ]);

        return redirect()->route('items.index')->with('success', 'Data Monitoring Berhasil Diperbarui.');
    }

    public function destroy(Item $item) {
        if (Auth::user()->hasRole('petugas')) abort(403);
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Aset Dihapus.');
    }
}