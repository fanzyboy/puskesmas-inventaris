@extends('layouts.app')

@section('content')
<h1 class="text-[28px] font-bold text-ink mb-8 leading-tight tracking-tight">Selamat datang di PuskesmasOps.</h1>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
    <!-- Stat Card 1 -->
    <div class="bg-white p-6 rounded-[14px] border border-hairline flex flex-col justify-between hover:airbnb-shadow transition-shadow">
        <div class="flex justify-between items-start mb-4">
            <p class="text-[14px] font-medium text-muted">Total Fasilitas</p>
            <i class="fa-solid fa-cubes text-primary text-xl"></i>
        </div>
        <p class="text-[28px] font-bold text-ink leading-none">{{ $total_items }}</p>
    </div>
    
    <!-- Stat Card 2 -->
    <div class="bg-white p-6 rounded-[14px] border border-hairline flex flex-col justify-between hover:airbnb-shadow transition-shadow">
        <div class="flex justify-between items-start mb-4">
            <p class="text-[14px] font-medium text-muted">Kondisi Baik</p>
            <i class="fa-solid fa-heart-pulse text-ink text-xl"></i>
        </div>
        <p class="text-[28px] font-bold text-ink leading-none">{{ $good_items }}</p>
    </div>
    
    <!-- Stat Card 3 -->
    <div class="bg-white p-6 rounded-[14px] border border-hairline flex flex-col justify-between hover:airbnb-shadow transition-shadow">
        <div class="flex justify-between items-start mb-4">
            <p class="text-[14px] font-medium text-muted">Barang Rusak</p>
            <i class="fa-solid fa-triangle-exclamation text-ink text-xl"></i>
        </div>
        <p class="text-[28px] font-bold text-ink leading-none">{{ $damaged_items }}</p>
    </div>
    
    <!-- Stat Card 4 -->
    <div class="bg-white p-6 rounded-[14px] border border-hairline flex flex-col justify-between hover:airbnb-shadow transition-shadow">
        <div class="flex justify-between items-start mb-4">
            <p class="text-[14px] font-medium text-muted">Sedang Dipinjam</p>
            <i class="fa-solid fa-business-time text-ink text-xl"></i>
        </div>
        <p class="text-[28px] font-bold text-ink leading-none">{{ $used_items }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Recent Activities (List Style) -->
    <div class="lg:col-span-2">
        <h2 class="text-[22px] font-semibold text-ink mb-6 tracking-tight">Aktivitas Terkini</h2>
        <div class="bg-white rounded-[14px] border border-hairline">
            <ul class="divide-y divide-hairline-soft">
                @forelse($recent_activities as $log)
                <li class="p-6 flex gap-4 items-start">
                    <div class="w-12 h-12 rounded-full bg-surface-soft flex-shrink-0 flex items-center justify-center text-ink border border-hairline overflow-hidden">
                        @if(str_contains(strtolower($log->action), 'pinjam') || str_contains(strtolower($log->action), 'kembali') || str_contains(strtolower($log->action), 'tolak'))
                            <i class="fa-solid fa-right-left"></i>
                        @else
                            <i class="fa-solid fa-pen"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-baseline mb-1">
                            <h3 class="text-[16px] font-semibold text-ink capitalize">{{ $log->user->name }}</h3>
                            <span class="text-[14px] text-muted">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[16px] text-body mb-2">{{ $log->action }} pada <span class="font-medium text-ink">{{ $log->item->name }}</span></p>
                    </div>
                </li>
                @empty
                <li class="p-8 text-center">
                    <p class="text-[16px] text-muted">Belum ada aktivitas monitoring hari ini.</p>
                </li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Room Density (Cards Style) -->
    <div>
        <h2 class="text-[22px] font-semibold text-ink mb-6 tracking-tight">Kepadatan Ruangan</h2>
        <div class="bg-white rounded-[14px] border border-hairline p-6">
            <div class="space-y-6">
                @foreach($rooms_chart as $room)
                <div>
                    <div class="flex justify-between text-[14px] font-medium mb-2">
                        <span class="text-ink">{{ $room->name }}</span>
                        <span class="text-muted">{{ $room->total_qty ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-surface-soft h-1.5 rounded-full overflow-hidden">
                        <div class="bg-primary h-1.5 rounded-full" style="width: {{ min(($room->total_qty ?? 0) * 2, 100) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection