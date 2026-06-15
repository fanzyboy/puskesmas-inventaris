<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Room;
use App\Models\ItemLog;
use App\Http\Requests\StoreItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class ItemController extends Controller
{
    public function index() {
        $user = Auth::user();
        $rooms = Room::all();
        
        // Data diambil urut berdasarkan nama ruangan dan status agar baris barang yang terpisah tetap berdekatan
        $query = Item::with('room')->orderBy('room_id')->orderBy('name')->orderBy('status');
        
        $items = $user->hasRole('admin') 
            ? $query->get() 
            : $query->where('room_id', $user->room_id)->get();

        return view('items.index', compact('items', 'rooms'));
    }

    public function store(StoreItemRequest $request) {
        $validated = $request->validated();

        // Cek apakah di ruangan tersebut sudah ada barang dengan nama & status yang SAMA
        $existingItem = Item::where('name', $validated['name'])
                            ->where('room_id', $validated['room_id'])
                            ->where('status', $validated['status'])
                            ->first();

        DB::transaction(function () use ($validated, $existingItem) {
            if ($existingItem) {
                // Jika nama dan status sama (misal sama-sama Baik), akumulasikan jumlahnya
                $oldValues = $existingItem->toArray();
                $existingItem->increment('qty', $validated['qty']);
                
                ItemLog::create([
                    'item_id' => $existingItem->id,
                    'user_id' => Auth::id(),
                    'action' => 'Penambahan Stok Fasilitas Eksis',
                    'old_values' => json_encode($oldValues),
                    'new_values' => json_encode($existingItem->fresh()->toArray())
                ]);
            } else {
                // Jika status berbeda (misal yang didaftarkan 'Rusak' tapi yang ada 'Baik'), buat baris BARU
                $validated['item_code'] = 'INV-' . strtoupper(substr($validated['category'], 0, 3)) . '-' . rand(1000, 9999);
                $newItem = Item::create($validated);

                ItemLog::create([
                    'item_id' => $newItem->id,
                    'user_id' => Auth::id(),
                    'action' => 'Registrasi Kategori Kondisi Baru',
                    'new_values' => json_encode($newItem->toArray())
                ]);
            }
        });

        return redirect()->route('items.index')->with('success', 'Aset Berhasil Diperbarui/Didaftarkan ke Sistem.');
    }

    public function update(Request $request, Item $item) {
        if (Auth::user()->hasRole('petugas') && $item->room_id != Auth::user()->room_id) {
            abort(403);
        }
    
        
$validated = $request->validate([
    'qty' => 'required|integer|min:1',
    'status' => 'required|in:Baik,Rusak,Tidak Tersedia,Digunakan,Dikembalikan' 
]);
    
        DB::transaction(function () use ($validated, $item) {
            // Jika statusnya sama, langsung timpa/update jumlah totalnya ke inputan baru
            if ($item->status === $validated['status']) {
                $oldValues = $item->toArray();
                $item->update(['qty' => $validated['qty']]);
                
                ItemLog::create([
                    'item_id' => $item->id,
                    'user_id' => Auth::id(),
                    'action' => 'Pembaruan Kuantitas Stok',
                    'old_values' => json_encode($oldValues),
                    'new_values' => json_encode($item->fresh()->toArray())
                ]);
            } else {
                // JIKA STATUS DIUBAH (Misal memindahkan sebagian atau input nominal baru)
                $inputQty = $validated['qty'];
    
                // 1. Kurangi atau sesuaikan baris asal jika inputan lebih kecil dari total item saat ini
                if ($inputQty < $item->qty) {
                    $item->decrement('qty', $inputQty);
                } else {
                    // Jika input qty lebih besar atau sama dengan stok asal, hapus baris lama karena semua pindah/berubah
                    $item->delete();
                }
    
                // 2. Cari atau buat baris baru dengan status tujuan di ruangan yang sama
                $targetItem = Item::where('name', $item->name)
                                  ->where('room_id', $item->room_id)
                                  ->where('status', $validated['status'])
                                  ->first();
    
                if ($targetItem) {
                    // Jika baris dengan status tujuan sudah ada, akumulasikan atau sesuaikan jumlahnya
                    $targetItem->increment('qty', $inputQty);
                } else {
                    // Jika belum ada, bikin baris kondisi baru
                    Item::create([
                        'item_code' => 'INV-' . strtoupper(substr($item->category, 0, 3)) . '-' . rand(1000, 9999),
                        'name' => $item->name,
                        'category' => $item->category,
                        'room_id' => $item->room_id,
                        'qty' => $inputQty,
                        'status' => $validated['status']
                    ]);
                }
            }
        });
    
        return redirect()->route('items.index')->with('success', 'Jumlah dan kondisi barang berhasil diperbarui.');
    }

    public function destroy(Item $item) {
        if (Auth::user()->hasRole('petugas')) abort(403);
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Aset Berhasil Dihapus dari Sistem.');
    }
}