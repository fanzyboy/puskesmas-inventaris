<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Room;
use Illuminate\Http\Request;
use Auth;

class ReportController extends Controller
{
    public function index(Request $request) {
        $rooms = Room::all();
        $query = Item::with('room');

        if (Auth::user()->hasRole('petugas')) {
            $query->where('room_id', Auth::user()->room_id);
        } elseif ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->get();
        return view('reports.index', compact('items', 'rooms'));
    }

    public function export(Request $request) {
        $query = Item::with('room');

        if (Auth::user()->hasRole('petugas')) {
            $query->where('room_id', Auth::user()->room_id);
        } elseif ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $items = $query->get();

        $fileName = 'Laporan-Aset-Puskesmas-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Kode Inventaris', 'Nama Barang', 'Kategori', 'Ruangan', 'Stok', 'Kondisi'];

        $callback = function() use($items, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($items as $item) {
                fputcsv($file, [$item->item_code, $item->name, $item->category, $item->room->name, $item->qty, $item->status]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}