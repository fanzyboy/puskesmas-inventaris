@extends('layouts.app')

@section('content')
<div class="mb-10 text-center max-w-2xl mx-auto">
    <h1 class="text-[32px] font-bold text-ink leading-tight tracking-tight mb-8">Laporan Inventaris</h1>

    <!-- Airbnb-style Pill Search Bar -->
    <div class="bg-white rounded-full border border-hairline airbnb-shadow flex items-center h-[66px] w-full max-w-[800px] mx-auto relative z-10">
        <form action="{{ route('reports.index') }}" method="GET" class="flex w-full h-full">
            
            <!-- Segment 1: Ruangan -->
            <div class="flex-1 h-full relative group">
                <div class="absolute inset-y-0 right-0 w-px bg-hairline"></div>
                @if(Auth::user()->hasRole('admin'))
                    <div class="h-full px-8 py-3 flex flex-col justify-center hover:bg-surface-soft rounded-l-full transition cursor-pointer">
                        <label class="text-[12px] font-bold text-ink mb-0.5">Ruangan</label>
                        <select name="room_id" class="w-full bg-transparent text-[14px] text-muted outline-none appearance-none cursor-pointer">
                            <option value="">Semua Ruangan</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="h-full px-8 py-3 flex flex-col justify-center hover:bg-surface-soft rounded-l-full transition cursor-pointer">
                        <label class="text-[12px] font-bold text-ink mb-0.5">Ruangan Anda</label>
                        <select name="room_id" class="w-full bg-transparent text-[14px] text-muted outline-none appearance-none cursor-pointer">
                            @foreach(Auth::user()->rooms as $room)
                                <option value="{{ $room->id }}" {{ request('room_id', session('active_room_id', Auth::user()->room_id)) == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <!-- Segment 2: Kondisi -->
            <div class="flex-1 h-full flex items-center pr-3 group relative">
                <div class="w-full h-full px-8 py-3 flex flex-col justify-center hover:bg-surface-soft rounded-full transition cursor-pointer">
                    <label class="text-[12px] font-bold text-ink mb-0.5">Kondisi</label>
                    <select name="status" class="w-full bg-transparent text-[14px] text-muted outline-none appearance-none cursor-pointer">
                        <option value="">Semua Kondisi</option>
                        <option value="Baik" {{ request('status') == 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="Rusak" {{ request('status') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                        <option value="Tidak Tersedia" {{ request('status') == 'Tidak Tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                        <option value="Digunakan" {{ request('status') == 'Digunakan' ? 'selected' : '' }}>Digunakan</option>
                        <option value="Dikembalikan" {{ request('status') == 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    </select>
                </div>
                
                <!-- Search Orb -->
                <button type="submit" class="w-[48px] h-[48px] rounded-full bg-primary hover:bg-primary-hover flex-shrink-0 flex items-center justify-center text-white transition ml-2">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="flex justify-end mb-4">
    <a href="{{ route('reports.export', request()->all()) }}" class="flex items-center gap-2 px-5 py-2.5 bg-white border border-ink rounded-lg text-[14px] font-medium text-ink hover:bg-surface-soft transition">
        <i class="fa-solid fa-arrow-down-to-line"></i> Export Data
    </a>
</div>

<!-- Clean Table Style -->
<div class="bg-white rounded-[14px] border border-hairline overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-[14px]">
            <thead>
                <tr class="border-b border-hairline bg-white">
                    <th class="px-6 py-4 font-semibold text-ink">Aset & Kode</th>
                    <th class="px-6 py-4 font-semibold text-ink">Kategori</th>
                    <th class="px-6 py-4 font-semibold text-ink">Lokasi</th>
                    <th class="px-6 py-4 font-semibold text-ink text-center">Stok</th>
                    <th class="px-6 py-4 font-semibold text-ink">Kondisi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-hairline-soft">
                @forelse($items as $item)
                <tr class="hover:bg-surface-soft/50 transition">
                    <td class="px-6 py-4">
                        <p class="font-medium text-ink">{{ $item->name }}</p>
                        <p class="text-[12px] text-muted">{{ $item->item_code }}</p>
                    </td>
                    <td class="px-6 py-4 text-muted">{{ $item->category }}</td>
                    <td class="px-6 py-4 text-ink">{{ $item->room->name }}</td>
                    <td class="px-6 py-4 text-center font-medium text-ink">{{ $item->qty }}</td>
                    <td class="px-6 py-4">
                        @if($item->status == 'Baik')
                            <span class="inline-flex items-center gap-1.5 text-ink"><span class="w-2 h-2 rounded-full bg-[#008a05]"></span>Baik</span>
                        @elseif($item->status == 'Rusak')
                            <span class="inline-flex items-center gap-1.5 text-ink"><span class="w-2 h-2 rounded-full bg-primary"></span>Rusak</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-muted"><span class="w-2 h-2 rounded-full bg-muted"></span>{{ $item->status }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-muted">Tidak ada data aset yang cocok dengan filter pencarian Anda.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection