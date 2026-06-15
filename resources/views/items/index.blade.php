@extends('layouts.app')

@section('content')
<div class="flex justify-between items-end mb-8">
    <div>
        <h1 class="text-[28px] font-bold text-ink leading-tight tracking-tight">Inventaris Fasilitas</h1>
        <p class="text-[16px] text-muted mt-1">Kelola dan pantau seluruh aset fisik Puskesmas.</p>
    </div>
    <button onclick="document.getElementById('add-modal').classList.remove('hidden')" class="px-6 py-3.5 bg-primary hover:bg-primary-hover text-white rounded-full text-[14px] font-medium transition flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Tambah Aset
    </button>
</div>

<!-- Property Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    @foreach($items as $item)
    <div class="group relative flex flex-col gap-3 cursor-pointer" onclick="document.getElementById('edit-modal-{{ $item->id }}').classList.remove('hidden')">
        <!-- Photo/Image Area (Placeholder) -->
        <div class="relative aspect-square w-full rounded-[14px] bg-surface-soft overflow-hidden border border-hairline flex items-center justify-center">
            <i class="fa-solid fa-box-archive text-[48px] text-hairline"></i>
            
            <!-- Badges -->
            @if($item->status === 'Baik')
                <div class="absolute top-3 left-3 bg-white text-ink text-[11px] font-semibold px-2.5 py-1 rounded-full airbnb-shadow">
                    Status Baik
                </div>
            @elseif($item->status === 'Rusak')
                <div class="absolute top-3 left-3 bg-white text-primary text-[11px] font-semibold px-2.5 py-1 rounded-full airbnb-shadow">
                    Rusak
                </div>
            @elseif($item->status === 'Digunakan')
                <div class="absolute top-3 left-3 bg-white text-ink text-[11px] font-semibold px-2.5 py-1 rounded-full airbnb-shadow">
                    Digunakan
                </div>
            @endif

            <!-- "Heart" Action Icon (Edit) -->
            <div class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/50 backdrop-blur-sm flex items-center justify-center text-ink hover:bg-white transition">
                <i class="fa-solid fa-pen-to-square text-sm"></i>
            </div>
        </div>

        <!-- Card Meta -->
        <div>
            <div class="flex justify-between items-start">
                <h3 class="text-[16px] font-semibold text-ink line-clamp-1" title="{{ $item->name }}">{{ $item->name }}</h3>
            </div>
            <p class="text-[14px] text-muted mt-0.5"><i class="fa-solid fa-location-dot mr-1"></i> {{ $item->room->name }}</p>
            <p class="text-[14px] text-muted">{{ $item->category }}</p>
            <p class="text-[14px] font-medium text-ink mt-1"><span class="font-bold">{{ $item->qty }}</span> Unit tersedia</p>
        </div>
    </div>

    <!-- Edit Modal for this item -->
    <div id="edit-modal-{{ $item->id }}" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-white w-full max-w-lg rounded-[14px] overflow-hidden airbnb-shadow text-left transform transition-all">
            <div class="px-6 py-4 border-b border-hairline flex justify-between items-center">
                <h3 class="text-[16px] font-semibold text-ink">Update Status Aset</h3>
                <button type="button" onclick="event.stopPropagation(); document.getElementById('edit-modal-{{ $item->id }}').classList.add('hidden')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-soft transition text-ink">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-6">
                <p class="text-[22px] font-semibold text-ink mb-6 tracking-tight">{{ $item->name }}</p>
                <form action="{{ route('items.update', $item->id) }}" method="POST" class="space-y-5">
                    @csrf @method('PUT')
                    <input type="hidden" name="name" value="{{ $item->name }}">
                    <div>
                        <label class="block text-[14px] font-medium text-ink mb-2">Jumlah Unit Tersedia</label>
                        <input type="number" name="qty" value="{{ $item->qty }}" required class="w-full px-4 py-3.5 bg-white border border-hairline focus:border-ink rounded-lg text-[16px] text-ink outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[14px] font-medium text-ink mb-2">Ubah Status Kondisi</label>
                        <div class="relative">
                            <select name="status" class="w-full px-4 py-3.5 bg-white border border-hairline focus:border-ink rounded-lg text-[16px] text-ink outline-none transition appearance-none">
                                <option value="Baik" {{ $item->status == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="Rusak" {{ $item->status == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                                <option value="Tidak Tersedia" {{ $item->status == 'Tidak Tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                                <option value="Digunakan" {{ $item->status == 'Digunakan' ? 'selected' : '' }}>Digunakan</option>
                                <option value="Dikembalikan" {{ $item->status == 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-ink text-sm pointer-events-none"></i>
                        </div>
                    </div>
                    <div class="pt-4 flex gap-3">
                        <button type="button" onclick="event.stopPropagation(); document.getElementById('edit-modal-{{ $item->id }}').classList.add('hidden')" class="flex-1 py-3.5 bg-white border border-ink text-ink rounded-lg text-[16px] font-medium hover:bg-surface-soft transition">Batal</button>
                        <button type="submit" class="flex-1 py-3.5 bg-ink text-white rounded-lg text-[16px] font-medium hover:bg-ink/90 transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($items->isEmpty())
<div class="py-20 text-center">
    <p class="text-[16px] text-muted">Belum ada aset inventaris yang terdaftar.</p>
</div>
@endif

<!-- Add Item Modal -->
<div id="add-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
    <div class="bg-white w-full max-w-lg rounded-[14px] overflow-hidden airbnb-shadow text-left">
        <div class="px-6 py-4 border-b border-hairline flex justify-between items-center">
            <h3 class="text-[16px] font-semibold text-ink">Registrasi Aset Baru</h3>
            <button type="button" onclick="document.getElementById('add-modal').classList.add('hidden')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-soft transition text-ink">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-6">
            <form action="{{ route('items.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[14px] font-medium text-ink mb-1">Nama Barang</label>
                    <input type="text" name="name" required class="w-full px-3 py-3 border border-hairline focus:border-ink rounded-lg text-[16px] text-ink outline-none transition">
                </div>
                <div>
                    <label class="block text-[14px] font-medium text-ink mb-1">Kategori</label>
                    <div class="relative">
                        <select name="category" class="w-full px-3 py-3 border border-hairline focus:border-ink rounded-lg text-[16px] text-ink outline-none transition appearance-none">
                            <option value="Alat Medis">Alat Medis</option>
                            <option value="Elektronik">Elektronik</option>
                            <option value="Mebel">Mebel</option>
                            <option value="Logistik">Logistik</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-ink text-sm pointer-events-none"></i>
                    </div>
                </div>
                <div>
                    <label class="block text-[14px] font-medium text-ink mb-1">Penempatan Ruangan</label>
                    <div class="relative">
                        <select name="room_id" class="w-full px-3 py-3 border border-hairline focus:border-ink rounded-lg text-[16px] text-ink outline-none transition appearance-none">
                            @foreach($rooms as $room)
                                @if(Auth::user()->hasRole('admin') || Auth::user()->room_id == $room->id)
                                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-ink text-sm pointer-events-none"></i>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[14px] font-medium text-ink mb-1">Kuantitas</label>
                        <input type="number" name="qty" min="1" value="1" required class="w-full px-3 py-3 border border-hairline focus:border-ink rounded-lg text-[16px] text-ink outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[14px] font-medium text-ink mb-1">Kondisi</label>
                        <div class="relative">
                            <select name="status" class="w-full px-3 py-3 border border-hairline focus:border-ink rounded-lg text-[16px] text-ink outline-none transition appearance-none">
                                <option value="Baik">Baik</option>
                                <option value="Rusak">Rusak</option>
                                <option value="Tidak Tersedia">Tidak Tersedia</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-ink text-sm pointer-events-none"></i>
                        </div>
                    </div>
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="document.getElementById('add-modal').classList.add('hidden')" class="flex-1 py-3.5 bg-white border border-ink text-ink rounded-lg text-[16px] font-medium hover:bg-surface-soft transition">Batal</button>
                    <button type="submit" class="flex-1 py-3.5 bg-primary hover:bg-primary-hover text-white rounded-lg text-[16px] font-medium transition">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection