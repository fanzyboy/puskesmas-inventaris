@extends('layouts.app')
@section('page_title', 'Manajemen Ruangan Puskesmas')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="bg-[#ffffff] p-6 rounded-[14px] border border-[#ebebeb] shadow-[rgba(0,0,0,0.01)_0_2px_4px] h-fit">
        <h3 class="text-[16px] font-semibold text-[#222222] mb-5">
            <i class="fa-solid fa-square-plus text-[#ff385c] mr-2"></i>Tambah Ruangan Baru
        </h3>
        
        <form action="{{ route('rooms.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="border border-[#dddddd] rounded-[8px] focus-within:border-[#222222] transition duration-200">
                <label class="block text-[11px] font-bold text-[#6a6a6a] uppercase px-3 pt-2 tracking-wide">Nama Ruangan</label>
                <input type="text" name="name" required placeholder="Contoh: Poli Mata" 
                       class="w-full px-3 pb-2 pt-0.5 bg-transparent border-none text-sm text-[#222222] focus:outline-none placeholder-[#929292]">
            </div>

            <div class="border border-[#dddddd] rounded-[8px] focus-within:border-[#222222] transition duration-200">
                <label class="block text-[11px] font-bold text-[#6a6a6a] uppercase px-3 pt-2 tracking-wide">Lantai Lokasi</label>
                <select name="location_floor" class="w-full px-3 pb-2 pt-0.5 bg-transparent border-none text-sm text-[#222222] focus:outline-none cursor-pointer">
                    <option value="1">Lantai 1</option>
                    <option value="2">Lantai 2</option>
                </select>
            </div>

            <div class="border border-[#dddddd] rounded-[8px] focus-within:border-[#222222] transition duration-200">
                <label class="block text-[11px] font-bold text-[#6a6a6a] uppercase px-3 pt-2 tracking-wide">Set Penanggung Jawab</label>
                <select name="user_id" class="w-full px-3 pb-2 pt-0.5 bg-transparent border-none text-sm text-[#222222] focus:outline-none cursor-pointer">
                    <option value="">-- Tanpa PJ Sementara --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" 
                    class="w-full h-[44px] bg-[#ff385c] hover:bg-[#e00b41] text-white rounded-[8px] text-sm font-medium transition duration-200 cursor-pointer shadow-sm">
                Simpan Ruangan
            </button>
        </form>
    </div>

    <div class="bg-[#ffffff] rounded-[14px] border border-[#ebebeb] shadow-[rgba(0,0,0,0.01)_0_2px_4px] lg:col-span-2 overflow-hidden">
        <div class="p-6 border-b border-[#f2f2f2] flex justify-between items-center">
            <h3 class="text-[16px] font-semibold text-[#222222]">Daftar Ruangan Aktif</h3>
            <span class="text-xs font-mono text-[#6a6a6a] bg-[#f7f7f7] px-2.5 py-1 rounded-full border border-[#ebebeb]">
                Total: {{ $rooms->count() }} Ruangan
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-[#f7f7f7] text-[#6a6a6a] text-[11px] font-bold uppercase tracking-wider border-b border-[#ebebeb]">
                        <th class="px-6 py-3.5">Nama Ruangan</th>
                        <th class="px-6 py-3.5">Lantai</th>
                        <th class="px-6 py-3.5">Penanggung Jawab</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#ebebeb] text-[#222222]">
                    @foreach($rooms as $room)
                    <tr class="hover:bg-[#f7f7f7]/60 transition duration-150">
                        <td class="px-6 py-4 font-medium text-[#222222] text-[15px]">{{ $room->name }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 bg-[#f2f2f2] text-[#222222] text-xs font-medium rounded-full border border-[#dddddd]">
                                Lt. {{ $room->location_floor }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-[#3f3f3f] text-sm">
                            @if($room->pj)
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full"></span> {{ $room->pj->name }}
                                </span>
                            @else
                                <span class="text-[#929292] italic text-xs">Belum Ditentukan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-4">
                                <button onclick="document.getElementById('edit-room-modal-{{ $room->id }}').classList.remove('hidden'); document.getElementById('edit-room-modal-{{ $room->id }}').classList.add('flex')" 
                                        class="text-[#222222] hover:text-[#ff385c] text-xs font-semibold underline underline-offset-2 transition cursor-pointer">
                                    <i class="fa-regular fa-pen-to-square mr-1"></i>Edit
                                </button>

                                <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" onsubmit="return confirm('Hapus ruangan ini akan melepaskan status keterikatan seluruh petugas di dalamnya!')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-[#c13515] hover:text-[#b32505] text-xs font-semibold underline underline-offset-2 transition cursor-pointer">
                                        <i class="fa-regular fa-trash-can mr-1"></i>Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <div id="edit-room-modal-{{ $room->id }}" class="hidden fixed inset-0 bg-[#000000]/50 backdrop-blur-xs items-center justify-center p-4 z-50 animate-fade-in">
                        <div class="bg-[#ffffff] w-full max-w-md rounded-[14px] p-6 shadow-[rgba(0,0,0,0.02)_0_0_0_1px,rgba(0,0,0,0.04)_0_2px_6px,rgba(0,0,0,0.1)_0_4px_8px] text-left border border-[#ebebeb]">
                            <div class="flex items-center justify-between border-b border-[#f2f2f2] pb-3 mb-5">
                                <h3 class="text-[16px] font-semibold text-[#222222]">
                                    <i class="fa-solid fa-sliders text-[#ff385c] mr-2"></i>Edit Informasi Ruangan
                                </h3>
                                <button type="button" onclick="document.getElementById('edit-room-modal-{{ $room->id }}').classList.add('hidden'); document.getElementById('edit-room-modal-{{ $room->id }}').classList.remove('flex')" 
                                        class="text-[#6a6a6a] hover:text-[#222222] text-lg cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            
                            <form action="{{ route('rooms.update', $room->id) }}" method="POST" class="space-y-4">
                                @csrf @method('PUT')
                                
                                <div class="border border-[#dddddd] rounded-[8px] focus-within:border-[#222222] transition">
                                    <label class="block text-[11px] font-bold text-[#6a6a6a] uppercase px-3 pt-2 tracking-wide">Nama Ruangan</label>
                                    <input type="text" name="name" value="{{ $room->name }}" required 
                                           class="w-full px-3 pb-2 pt-0.5 bg-transparent border-none text-sm text-[#222222] focus:outline-none">
                                </div>
                                
                                <div class="border border-[#dddddd] rounded-[8px] focus-within:border-[#222222] transition">
                                    <label class="block text-[11px] font-bold text-[#6a6a6a] uppercase px-3 pt-2 tracking-wide">Lantai Lokasi</label>
                                    <select name="location_floor" class="w-full px-3 pb-2 pt-0.5 bg-transparent border-none text-sm text-[#222222] focus:outline-none cursor-pointer">
                                        <option value="1" {{ $room->location_floor == '1' ? 'selected' : '' }}>Lantai 1</option>
                                        <option value="2" {{ $room->location_floor == '2' ? 'selected' : '' }}>Lantai 2</option>
                                    </select>
                                </div>
                                
                                <div class="border border-[#dddddd] rounded-[8px] focus-within:border-[#222222] transition">
                                    <label class="block text-[11px] font-bold text-[#6a6a6a] uppercase px-3 pt-2 tracking-wide">Ubah Penanggung Jack</label>
                                    <select name="user_id" class="w-full px-3 pb-2 pt-0.5 bg-transparent border-none text-sm text-[#222222] focus:outline-none cursor-pointer">
                                        <option value="">-- Tanpa PJ Sementara --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ ($room->user_id == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="flex justify-end gap-3 pt-3 border-t border-[#f2f2f2] mt-6">
                                    <button type="button" onclick="document.getElementById('edit-room-modal-{{ $room->id }}').classList.add('hidden'); document.getElementById('edit-room-modal-{{ $room->id }}').classList.remove('flex')" 
                                            class="px-4 h-[40px] text-sm bg-[#f7f7f7] hover:bg-[#f2f2f2] text-[#222222] rounded-[8px] font-medium transition cursor-pointer border border-[#dddddd]">Batal</button>
                                    <button type="submit" 
                                            class="px-4 h-[40px] text-sm bg-[#ff385c] hover:bg-[#e00b41] text-white rounded-[8px] font-medium transition cursor-pointer shadow-sm">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection