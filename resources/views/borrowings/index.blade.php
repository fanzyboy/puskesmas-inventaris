@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-[28px] font-bold text-ink leading-tight tracking-tight">Mutasi & Peminjaman</h1>
    <p class="text-[16px] text-muted mt-1">Ajukan dan pantau pergerakan aset antar ruangan.</p>
</div>

<div class="flex flex-col-reverse lg:flex-row gap-12">
    <!-- Left Column: History List -->
    <div class="flex-1">
        <h2 class="text-[22px] font-semibold text-ink mb-6 tracking-tight">Riwayat Pengajuan</h2>
        
        <div class="space-y-6">
            @forelse($borrowings as $b)
            <div class="pb-6 border-b border-hairline-soft last:border-0">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="text-[16px] font-semibold text-ink">
                            {{ $b->details->first()->item->name ?? 'Aset Terhapus' }}
                        </h3>
                        <p class="text-[14px] text-muted mt-0.5">Kuantitas: {{ $b->details->first()->qty ?? 0 }} Unit &middot; Kode: {{ $b->borrow_code }}</p>
                    </div>
                    
                    <!-- Status Badge -->
                    @if($b->status == 'Pending')
                        <span class="px-3 py-1 bg-surface-soft text-ink text-[12px] font-semibold rounded-full border border-hairline">Menunggu</span>
                    @elseif($b->status == 'Approved')
                        <span class="px-3 py-1 bg-ink text-white text-[12px] font-semibold rounded-full">Disetujui</span>
                    @elseif($b->status == 'Returned')
                        <span class="px-3 py-1 bg-surface-soft text-ink text-[12px] font-semibold rounded-full border border-hairline">Dikembalikan</span>
                    @elseif($b->status == 'Rejected')
                        <span class="px-3 py-1 bg-primary text-white text-[12px] font-semibold rounded-full">Ditolak</span>
                    @endif
                </div>

                <div class="flex items-center gap-2 text-[14px] text-muted mt-2">
                    <span class="font-medium text-ink">{{ $b->fromRoom->name }}</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    <span class="font-medium text-ink">{{ $b->toRoom->name }}</span>
                </div>

                @if(Auth::user()->hasRole('admin'))
                    <div class="mt-4 flex gap-3">
                        @if($b->status === 'Pending')
                            <form action="{{ route('borrowings.action', $b->id) }}" method="POST" class="m-0">
                                @csrf <input type="hidden" name="status" value="Approved">
                                <button class="px-4 py-2 bg-ink text-white rounded-lg text-[14px] font-medium hover:bg-ink/90 transition">Setujui</button>
                            </form>
                            <form action="{{ route('borrowings.action', $b->id) }}" method="POST" class="m-0">
                                @csrf <input type="hidden" name="status" value="Rejected">
                                <button class="px-4 py-2 bg-white border border-ink text-ink rounded-lg text-[14px] font-medium hover:bg-surface-soft transition">Tolak</button>
                            </form>
                        @elseif($b->status === 'Approved')
                            <form action="{{ route('borrowings.action', $b->id) }}" method="POST" class="m-0">
                                @csrf <input type="hidden" name="status" value="Returned">
                                <button class="px-4 py-2 bg-white border border-ink text-ink rounded-lg text-[14px] font-medium hover:bg-surface-soft transition">
                                    <i class="fa-solid fa-rotate-left mr-2"></i>Tandai Dikembalikan
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
            @empty
            <div class="py-10 text-center">
                <p class="text-[16px] text-muted">Belum ada riwayat mutasi atau peminjaman.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Right Column: Form (Reservation Card Style) -->
    <div class="w-full lg:w-[380px] flex-shrink-0">
        <div class="bg-white p-6 rounded-[14px] border border-hairline airbnb-shadow sticky top-28">
            <h3 class="text-[22px] font-semibold text-ink mb-6 tracking-tight">Ajukan Peminjaman</h3>
            <form action="{{ route('borrowings.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Inputs wrapped in a border like Airbnb dates -->
                <div class="border border-hairline rounded-lg overflow-hidden">
                    <div class="p-3 border-b border-hairline bg-white">
                        <label class="block text-[10px] font-bold text-ink uppercase tracking-wider mb-1">Aset (Kondisi Baik)</label>
                        <div class="relative">
                            <select name="item_id" class="w-full bg-transparent text-[14px] text-ink outline-none appearance-none font-medium">
                                @foreach($allItems as $item)
                                    @if($item->room_id != Auth::user()->room_id)
                                    <option value="{{ $item->id }}">{{ $item->name }} (Asal: {{ $item->room->name }} - {{ $item->qty }} Unit)</option>
                                    @endif
                                @endforeach
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-0 top-1/2 -translate-y-1/2 text-ink text-xs pointer-events-none"></i>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="flex-1 p-3 border-r border-hairline bg-white">
                            <label class="block text-[10px] font-bold text-ink uppercase tracking-wider mb-1">Jumlah</label>
                            <input type="number" name="qty" min="1" required placeholder="1" class="w-full bg-transparent text-[14px] text-ink outline-none font-medium placeholder-muted">
                        </div>
                        <div class="flex-1 p-3 bg-white">
                            <label class="block text-[10px] font-bold text-ink uppercase tracking-wider mb-1">Tgl Digunakan</label>
                            <input type="date" name="borrow_date" min="{{ date('Y-m-d') }}" required class="w-full bg-transparent text-[14px] text-ink outline-none font-medium">
                        </div>
                    </div>
                </div>

                <div class="border border-hairline rounded-lg p-3 bg-white">
                    <label class="block text-[10px] font-bold text-ink uppercase tracking-wider mb-1">Keperluan</label>
                    <textarea name="notes" placeholder="Tulis alasan peminjaman mendesak..." rows="2" class="w-full bg-transparent text-[14px] text-ink outline-none font-medium resize-none placeholder-muted"></textarea>
                </div>

                <button type="submit" class="w-full py-3.5 mt-2 bg-primary hover:bg-primary-hover text-white rounded-lg text-[16px] font-medium transition">
                    Kirim Pengajuan
                </button>
                
                <p class="text-[12px] text-center text-muted mt-4">Pengajuan Anda akan ditinjau oleh Admin.</p>
            </form>
        </div>
    </div>
</div>
@endsection