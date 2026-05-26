@extends('layouts.app')
@section('page_title', 'Log Mutasi & Peminjaman Barang Antar Ruangan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm h-fit">
        <h3 class="text-base font-bold text-slate-900 mb-4"><i class="fa-solid fa-paper-plane text-sky-500 mr-2"></i>Form Pengajuan Pinjam</h3>
        <form action="{{ route('borrowings.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Pilih Barang (Kondisi Baik)</label>
                <select name="item_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                    @foreach($allItems as $item)
                        @if($item->room_id != Auth::user()->room_id)
                        <option value="{{ $item->id }}">{{ $item->name }} (Asal: {{ $item->room->name }} - Sisa: {{ $item->qty }} U)</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Jumlah</label>
                    <input type="number" name="qty" min="1" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tgl Digunakan</label>
                    <input type="date" name="borrow_date" min="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Alasan/Keperluan</label>
                <textarea name="notes" placeholder="Tulis alasan peminjaman mendesak..." rows="3" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none"></textarea>
            </div>
            <button type="submit" class="w-full py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg text-sm font-semibold shadow transition">Kirim Pengajuan</button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm lg:col-span-2 overflow-hidden">
        <div class="p-6 border-b border-slate-100"><h3 class="text-base font-bold text-slate-900">Histori & Status Dokumen</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 text-xs font-bold uppercase border-b border-slate-100">
                        <th class="px-6 py-3">Kode</th>
                        <th class="px-6 py-3">Barang (Qty)</th>
                        <th class="px-6 py-3">Rute Mutasi</th>
                        <th class="px-6 py-3">Status</th>
                        @if(Auth::user()->hasRole('admin')) <th class="px-6 py-3 text-center">Otorisasi</th> @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach($borrowings as $b)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 font-mono text-xs text-slate-400">{{ $b->borrow_code }}</td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-slate-900">
                                {{ $b->details->first()->item->name ?? 'Aset Terhapus' }}
                            </span>
                            <span class="text-xs text-slate-500 block">Kuantitas: {{ $b->details->first()->qty ?? 0 }} Unit</span>
                        </td>
                        <td class="px-6 py-4 text-xs font-medium text-slate-600">
                            <div class="flex items-center gap-1">
                                <span class="text-rose-600">{{ $b->fromRoom->name }}</span>
                                <i class="fa-solid fa-arrow-right-long text-slate-400 text-[10px]"></i>
                                <span class="text-emerald-600">{{ $b->toRoom->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 text-xs font-bold rounded 
                                {{ $b->status == 'Pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                {{ $b->status == 'Approved' ? 'bg-sky-100 text-sky-800' : '' }}
                                {{ $b->status == 'Returned' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                {{ $b->status == 'Rejected' ? 'bg-rose-100 text-rose-800' : '' }}
                            ">{{ $b->status }}</span>
                        </td>
                        @if(Auth::user()->hasRole('admin'))
                        <td class="px-6 py-4 text-center">
                            <div class="flex gap-2 justify-center">
                                @if($b->status === 'Pending')
                                    <form action="{{ route('borrowings.action', $b->id) }}" method="POST">
                                        @csrf <input type="hidden" name="status" value="Approved">
                                        <button class="bg-sky-600 text-white px-2 py-1 text-xs font-bold rounded shadow-sm hover:bg-sky-700">Setujui</button>
                                    </form>
                                    <form action="{{ route('borrowings.action', $b->id) }}" method="POST">
                                        @csrf <input type="hidden" name="status" value="Rejected">
                                        <button class="bg-slate-100 text-slate-600 px-2 py-1 text-xs font-bold rounded hover:bg-slate-200">Tolak</button>
                                    </form>
                                @elseif($b->status === 'Approved')
                                    <form action="{{ route('borrowings.action', $b->id) }}" method="POST">
                                        @csrf <input type="hidden" name="status" value="Returned">
                                        <button class="bg-emerald-600 text-white px-2 py-1 text-xs font-bold rounded shadow-sm hover:bg-emerald-700"><i class="fa-solid fa-rotate-left mr-1"></i>Kembalikan</button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">- Selesai -</span>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection