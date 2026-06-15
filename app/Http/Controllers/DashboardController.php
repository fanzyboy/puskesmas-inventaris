<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Room;
use App\Models\ItemLog;
use Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Cek scoping jika petugas
        $itemQuery = Item::query();
        if ($user->hasRole('petugas')) {
            $itemQuery->where('room_id', $user->room_id);
        }

        $data['total_items'] = (clone $itemQuery)->where('status', '!=', 'Dikembalikan')->sum('qty');
        $data['good_items'] = (clone $itemQuery)->where('status', 'Baik')->sum('qty');
        $data['damaged_items'] = (clone $itemQuery)->where('status', 'Rusak')->sum('qty');
        $data['used_items'] = (clone $itemQuery)->where('status', 'Digunakan')->sum('qty');

        $data['rooms_chart'] = Room::withCount(['items as total_qty' => function($q) {
            $q->where('status', '!=', 'Dikembalikan')->select(\DB::raw('sum(qty)'));
        }])->orderBy('total_qty', 'desc')->take(5)->get();

        $logQuery = ItemLog::with(['item', 'user'])->latest();
        if ($user->hasRole('petugas')) {
            $logQuery->whereHas('item', function($q) use ($user) {
                $q->where('room_id', $user->room_id);
            });
        }
        $data['recent_activities'] = $logQuery->take(5)->get();

        return view('dashboard.index', $data);
    }
}