@extends('layouts.app')
@section('page_title', 'Real-time Dashboard Monitoring')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Fasilitas</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-1">{{ $total_items }} <span class="text-xs font-normal text-slate-400">Unit</span></p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600 text-xl shadow-inner"><i class="fa-solid fa-cubes"></i></div>
    </div>
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kondisi Baik</p>
            <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $good_items }} <span class="text-xs font-normal text-slate-400">Unit</span></p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl shadow-inner"><i class="fa-solid fa-heart-pulse"></i></div>
    </div>
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Barang Rusak</p>
            <p class="text-3xl font-extrabold text-rose-600 mt-1">{{ $damaged_items }} <span class="text-xs font-normal text-slate-400">Unit</span></p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 text-xl shadow-inner"><i class="fa-solid fa-triangle-exclamation"></i></div>
    </div>
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sedang Dipinjam</p>
            <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ $used_items }} <span class="text-xs font-normal text-slate-400">Unit</span></p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-xl shadow-inner"><i class="fa-solid fa-business-time"></i></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm lg:col-span-2">
        <h3 class="text-base font-bold text-slate-900 mb-4"><i class="fa-solid fa-circle-nodes text-sky-500 mr-2"></i>Aktivitas Mutasi & Log Terkini</h3>
        <div class="flow-root">
            <ul class="-mb-8">
                @forelse($recent_activities as $log)
                <li>
                    <div class="relative pb-6">
                        @if(!$loop->last)<span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200"></span>@endif
                        <div class="relative flex space-x-3">
                            <span class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 text-xs shadow-sm border border-slate-200">
                                <i class="fa-solid fa-history"></i>
                            </span>
                            <div class="flex-1 min-w-0 pt-1.5 flex justify-between space-x-4">
                                <p class="text-sm text-slate-600"><strong>{{ $log->user->name }}</strong>: <span class="text-sky-600 font-medium">{{ $log->action }}</span> pada <strong>{{ $log->item->name }}</strong></p>
                                <div class="text-right text-xs text-slate-400 font-medium">{{ $log->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                </li>
                @empty
                <p class="text-sm text-slate-400 py-4 text-center">Belum ada aktivitas monitoring hari ini.</p>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 mb-4"><i class="fa-solid fa-chart-bar text-sky-500 mr-2"></i>Kepadatan Aset Ruangan</h3>
        <div class="space-y-4">
            @foreach($rooms_chart as $room)
            <div>
                <div class="flex justify-between text-xs font-semibold mb-1">
                    <span class="text-slate-700">{{ $room->name }}</span>
                    <span class="text-slate-500">{{ $room->total_qty ?? 0 }} Unit</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-sky-500 h-2 rounded-full" style="width: {{ min(($room->total_qty ?? 0) * 2, 100) }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection