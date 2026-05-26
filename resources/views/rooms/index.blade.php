@extends('layouts.app')
@section('page_title', 'Manajemen 20 Ruangan Puskesmas')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm h-fit">
        <h3 class="text-base font-bold text-slate-900 mb-4"><i class="fa-solid fa-square-plus text-sky-500 mr-2"></i>Tambah Ruangan Baru</h3>
        <form action="{{ route('rooms.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Ruangan</label>
                <input type="text" name="name" required placeholder="Contoh: Poli Mata" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-sky-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Lantai Lokasi</label>
                <select name="location_floor" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-sky-500">
                    <option value="1">Lantai 1</option>
                    <option value="2">Lantai 2</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Set Penanggung Jawab</label>
                <select name="user_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-sky-500">
                    <option value="">-- Tanpa PJ Sementara --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</button>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="w-full py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-sm font-semibold shadow shadow-sky-200 transition">Simpan Ruangan</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm lg:col-span-2 overflow-hidden">
        <div class="p-6 border-b border-slate-100"><h3 class="text-base font-bold text-slate-900">Daftar Ruangan Aktif</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 text-xs font-bold uppercase border-b border-slate-100">
                        <th class="px-6 py-3">Nama Ruangan</th>
                        <th class="px-6 py-3">Lantai</th>
                        <th class="px-6 py-3">Penanggung Jawab</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($rooms as $room)
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-6 py-4 font-semibold text-slate-900">{{ $room->name }}</td>
                        <td class="px-6 py-4"><span class="px-2 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-md">Lt. {{ $room->location_floor }}</span></td>
                        <td class="px-6 py-4 text-slate-500">{{ $room->pj->name ?? 'Belum Ditentukan' }}</td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" onsubmit="return confirm('Hapus ruangan ini akan menghapus seluruh data barang di dalamnya!')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold"><i class="fa-regular fa-trash-can mr-1"></i>Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection