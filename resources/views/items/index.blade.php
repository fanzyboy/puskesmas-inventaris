@extends('layouts.app')
@section('page_title', 'Sistem Informasi Inventaris Fasilitas')

@section('content')
<div class="mb-6 flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
    <p class="text-sm text-slate-500 font-medium">Menampilkan data inventaris terverifikasi sistem.</p>
    <button onclick="document.getElementById('add-modal').classList.toggle('hidden')" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-sm font-semibold shadow transition"><i class="fa-solid fa-plus mr-2"></i>Registrasi Barang Baru</button>
</div>

<div id="add-modal" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm items-center justify-center p-4 z-50">
    <div class="bg-white w-full max-w-md rounded-xl p-6 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 mb-4">Form Registrasi Unit Aset</h3>
        <form action="{{ route('items.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Barang</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kategori</label>
                <select name="category" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                    <option value="Alat Medis">Alat Medis</option>
                    <option value="Elektronik">Elektronik</option>
                    <option value="Mebel">Mebel</option>
                    <option value="Logistik">Logistik</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Penempatan Ruangan</label>
                <select name="room_id" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                    @foreach($rooms as $room)
                        @if(Auth::user()->hasRole('admin') || Auth::user()->room_id == $room->id)
                            <option value="{{ $room->id }}">{{ $room->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kuantitas Awal</label>
                    <input type="number" name="qty" min="1" value="1" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kondisi Mula</label>
                    <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                        <option value="Baik">Baik</option>
                        <option value="Rusak">Rusak</option>
                        <option value="Tidak Tersedia">Tidak Tersedia</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('add-modal').classList.add('hidden')" class="px-4 py-2 text-sm bg-slate-100 text-slate-600 rounded-lg font-medium">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm bg-sky-600 text-white rounded-lg font-semibold">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full text-left text-sm border-collapse">
        <thead>
            <tr class="bg-slate-50 text-slate-400 text-xs font-bold uppercase border-b border-slate-100">
                <th class="px-6 py-3">Kode</th>
                <th class="px-6 py-3">Nama Barang</th>
                <th class="px-6 py-3">Kategori</th>
                <th class="px-6 py-3">Ruangan</th>
                <th class="px-6 py-3 text-center">Jumlah</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-right">Update Kondisi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
            @foreach($items as $item)
            <tr class="hover:bg-slate-50/50">
                <td class="px-6 py-4 font-mono text-xs font-bold text-slate-400">{{ $item->item_code }}</td>
                <td class="px-6 py-4 font-semibold text-slate-900">{{ $item->name }}</td>
                <td class="px-6 py-4 text-slate-500 text-xs">{{ $item->category }}</td>
                <td class="px-6 py-4 text-slate-600 font-medium"><i class="fa-regular fa-map-marker-alt text-sky-500 mr-1.5"></i>{{ $item->room->name }}</td>
                <td class="px-6 py-4 text-center font-bold">{{ $item->qty }}</td>
                <td class="px-6 py-4">
                    <span class="px-2.5 py-1 text-xs font-bold rounded-full 
                        {{ $item->status === 'Baik' ? 'bg-emerald-50 text-emerald-700' : '' }}
                        {{ $item->status === 'Rusak' ? 'bg-rose-50 text-rose-700' : '' }}
                        {{ $item->status === 'Digunakan' ? 'bg-amber-50 text-amber-700' : '' }}
                        {{ $item->status === 'Tidak Tersedia' ? 'bg-slate-100 text-slate-600' : '' }}
                    ">{{ $item->status }}</span>
                </td>
                <td class="px-6 py-4 text-right">
                    <button onclick="document.getElementById('edit-modal-{{ $item->id }}').classList.toggle('hidden')" class="px-2 py-1 border border-slate-200 text-xs font-bold text-slate-600 bg-white shadow-sm rounded-md hover:bg-slate-50"><i class="fa-solid fa-pen-to-square text-sky-500 mr-1"></i> Monitoring Status</button>
                </td>
            </tr>

            <div id="edit-modal-{{ $item->id }}" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm items-center justify-center p-4 z-50">
                <div class="bg-white w-full max-w-md rounded-xl p-6 shadow-2xl text-left">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Update Log Kualitas: {{ $item->name }}</h3>
                    <form action="{{ route('items.update', $item->id) }}" method="POST" class="space-y-4">
                        @csrf @method('PUT')
                        <input type="hidden" name="name" value="{{ $item->name }}">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jumlah Unit Tersedia</label>
                            <input type="number" name="qty" value="{{ $item->qty }}" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ubah Status Kondisi</label>
                            <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm">
                                <option value="Baik" {{ $item->status == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="Rusak" {{ $item->status == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                                <option value="Tidak Tersedia" {{ $item->status == 'Tidak Tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                                <option value="Digunakan" {{ $item->status == 'Digunakan' ? 'selected' : '' }}>Digunakan</option>
                            </select>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" onclick="document.getElementById('edit-modal-{{ $item->id }}').classList.add('hidden')" class="px-4 py-2 text-sm bg-slate-100 text-slate-600 rounded-lg font-medium">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm bg-sky-600 text-white rounded-lg font-semibold">Terapkan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>
@endsection