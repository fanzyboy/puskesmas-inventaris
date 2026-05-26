<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Item;
use App\Http\Requests\StoreBorrowingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class BorrowingController extends Controller
{
    public function index() {
        $user = Auth::user();
        
        $query = Borrowing::with(['requester', 'fromRoom', 'toRoom', 'details.item']);
        if ($user->hasRole('petugas')) {
            $query->where('requester_id', $user->id)
                  ->orWhere('from_room_id', $user->room_id);
        }
        $borrowings = $query->latest()->get();
        $allItems = Item::with('room')->where('status', 'Baik')->where('qty', '>', 0)->get();

        return view('borrowings.index', compact('borrowings', 'allItems'));
    }

    public function store(StoreBorrowingRequest $request) {
        $user = Auth::user();
        if (!$user->room_id) {
            return back()->withErrors(['msg' => 'Anda belum ditempatkan di ruangan mana pun.']);
        }

        $item = Item::findOrFail($request->item_id);

        if ($item->room_id == $user->room_id) {
            return back()->withErrors(['msg' => 'Tidak bisa meminjam barang dari ruangan sendiri.']);
        }

        if ($item->qty < $request->qty) {
            return back()->withErrors(['msg' => 'Stok ruangan asal tidak mencukupi.']);
        }

        DB::transaction(function () use ($request, $user, $item) {
            $borrow = Borrowing::create([
                'borrow_code' => 'REQ-' . time() . '-' . rand(10,99),
                'requester_id' => $user->id,
                'from_room_id' => $item->room_id,
                'to_room_id' => $user->room_id,
                'borrow_date' => $request->borrow_date,
                'status' => 'Pending',
                'notes' => $request->notes
            ]);

            BorrowingDetail::create([
                'borrowing_id' => $borrow->id,
                'item_id' => $item->id,
                'qty' => $request->qty
            ]);
        });

        return redirect()->route('borrowings.index')->with('success', 'Permintaan Peminjaman Berhasil Dikirim.');
    }

    public function action(Request $request, Borrowing $borrowing) {
        if (!Auth::user()->hasRole('admin')) abort(403);
        $request->validate(['status' => 'required|in:Approved,Rejected,Returned']);

        DB::transaction(function () use ($request, $borrowing) {
            $status = $request->status;
            
            if ($status === 'Approved' && $borrowing->status === 'Pending') {
                foreach ($borrowing->details as $detail) {
                    $srcItem = $detail->item;
                    $srcItem->decrement('qty', $detail->qty);

                    $targetItem = Item::where('name', $srcItem->name)
                        ->where('room_id', $borrowing->to_room_id)
                        ->where('status', 'Digunakan')
                        ->first();

                    if ($targetItem) {
                        $targetItem->increment('qty', $detail->qty);
                    } else {
                        Item::create([
                            'item_code' => 'INV-MUT-' . rand(1000,9999),
                            'name' => $srcItem->name,
                            'category' => $srcItem->category,
                            'room_id' => $borrowing->to_room_id,
                            'qty' => $detail->qty,
                            'status' => 'Digunakan'
                        ]);
                    }
                }
            } 
            
            if ($status === 'Returned' && $borrowing->status === 'Approved') {
                foreach ($borrowing->details as $detail) {
                    // Balikkan stok ke asal
                    Item::where('name', $detail->item->name)
                        ->where('room_id', $borrowing->from_room_id)
                        ->where('status', 'Baik')
                        ->increment('qty', $detail->qty);

                    // Kurangi stok di peminjam
                    $renterItem = Item::where('name', $detail->item->name)
                        ->where('room_id', $borrowing->to_room_id)
                        ->where('status', 'Digunakan')
                        ->first();
                    
                    if ($renterItem) {
                        $renterItem->decrement('qty', $detail->qty);
                    }
                }
                $borrowing->return_date = now();
            }

            $borrowing->status = $status;
            $borrowing->approved_by = Auth::id();
            $borrowing->save();
        });

        return redirect()->route('borrowings.index')->with('success', 'Status Transaksi Berhasil Diperbarui.');
    }
}