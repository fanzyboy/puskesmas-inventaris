@extends('layouts.app')
@section('page_title', 'Sistem Generator Laporan Otomatis')

@section('content')
<div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-8">
    <h3 class="text-base font-bold text-slate-900 mb-4"><i class="fa-solid fa-filter text-sky-500 mr-2"></i>Filter Data Inventaris Fisik</h3>
    <form action="{{ route('reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        @if(Auth::user()->hasRole('admin'))
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Berdasarkan Ruangan</label>
            <select name="room_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                <option value="">-- Semua Ruangan Puskesmas --</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                @endforeach
            </select>
        </div>
        @else
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ruangan Anda</label>
            <input type="text" disabled value="{{ Auth::user()->room->name }}" class="w-full px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-sm text-slate-500 font-semibold">
        </div>
        @endif
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Filter Kondisi Barang</label>
            <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                <option value="">-- Semua Kondisi --</option>
                <option value="Baik" {{ request('status') == 'Baik' ? 'selected' : '' }}>Baik</option>
                <option value="Rusak" {{ request('status') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                <option value="Tidak Tersedia" {{ request('status') == 'Tidak Tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                <option value="Digunakan" {{ request('status') == 'Digunakan' ? 'selected' : '' }}>Digunakan</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold transition shadow"><i class="fa-solid fa-magnifying-glass mr-2"></i>Cari</button>
            <a href="{{ route('reports.export', request()->all()) }}" class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-center rounded-lg text-sm font-semibold transition shadow"><i class="fa-solid fa-file-excel mr-2"></i>Export Excel/CSV</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full text-left text-sm border-collapse">
        <thead>
            <tr class="bg-slate-50 text-slate-400 text-xs font-bold uppercase border-b border-slate-100">
                <th class="px-6 py-3">Kode Aset</th>
                <th class="px-6 py-3">Nama Alat / Fasilitas</th>
                <th class="px-6 py-3">Kategori</th>
                <th class="px-6 py-3">Lokasi Kamar</th>
                <th class="px-6 py-3 text-center">Stok</th>
                <th class="px-6 py-3">Kondisi Saat Ini</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
            @forelse($items as $item)
            <tr class="hover:bg-slate-50/50">
                <td class="px-6 py-4 font-mono text-xs font-bold text-slate-400">{{ $item->item_code }}</td>
                <td class="px-6 py-4 font-semibold text-slate-900">{{ $item->name }}</td>
                <td class="px-6 py-4 text-xs">{{ $item->category }}</td>
                <td class="px-6 py-4 font-medium"><i class="fa-solid fa-location-dot text-slate-400 mr-1.5"></i>{{ $item->room->name }}</td>
                <td class="px-6 py-4 text-center font-bold">{{ $item->qty }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-0.5 text-xs font-bold rounded-md
                        {{ $item->status === 'Baik' ? 'bg-emerald-50 text-emerald-700' : '' }}
                        {{ $item->status === 'Rusak' ? 'bg-rose-50 text-rose-700' : '' }}
                        {{ $item->status === 'Digunakan' ? 'bg-amber-50 text-amber-700' : '' }}
                        {{ $item->status === 'Tidak Tersedia' ? 'bg-slate-100 text-slate-600' : '' }}
                    ">{{ $item->status }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400 font-medium">Tidak ada data aset yang cocok dengan filter pencarian Anda.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection