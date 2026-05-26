<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris Puskesmas</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-slate-900 text-white flex flex-col justify-between p-4 shadow-xl">
            <div>
                <div class="flex items-center gap-3 px-2 py-4 border-b border-slate-800 mb-6">
                    <i class="fa-solid fa-hospital text-sky-400 text-2xl"></i>
                    <span class="font-bold text-lg tracking-wider">Puskesmas-Ops</span>
                </div>
                <nav class="space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition {{ request()->routeIs('dashboard') ? 'bg-sky-600 text-white font-semibold' : '' }}">
                        <i class="fa-solid fa-chart-pie w-5"></i> Dashboard
                    </a>
                    @if(Auth::user()->hasRole('admin'))
                    <a href="{{ route('rooms.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition {{ request()->routeIs('rooms.*') ? 'bg-sky-600 text-white font-semibold' : '' }}">
                        <i class="fa-solid fa-door-open w-5"></i> Manajemen Ruangan
                    </a>
                    @endif
                    <a href="{{ route('items.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition {{ request()->routeIs('items.*') ? 'bg-sky-600 text-white font-semibold' : '' }}">
                        <i class="fa-solid fa-boxes-stacked w-5"></i> Inventaris Barang
                    </a>
                    <a href="{{ route('borrowings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition {{ request()->routeIs('borrowings.*') ? 'bg-sky-600 text-white font-semibold' : '' }}">
                        <i class="fa-solid fa-handshake w-5"></i> Peminjaman Barang
                    </a>
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition {{ request()->routeIs('reports.*') ? 'bg-sky-600 text-white font-semibold' : '' }}">
                        <i class="fa-solid fa-file-invoice w-5"></i> Laporan Otomatis
                    </a>
                </nav>
            </div>
            <div class="border-t border-slate-800 pt-4">
                <div class="flex items-center gap-3 px-2 py-2 mb-4 bg-slate-800/50 rounded-lg">
                    <div class="w-9 h-9 rounded-full bg-sky-500 flex items-center justify-center text-sm font-bold text-white shadow">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-400 capitalize">{{ Auth::user()->role->display_name }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-400 hover:bg-red-950/30 rounded-lg transition font-medium">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar Aplikasi
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">@yield('page_title')</h2>
                <div class="flex items-center gap-2 text-sm text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg">
                    <i class="fa-regular fa-calendar"></i>
                    <span>{{ now()->translatedFormat('d F Y') }}</span>
                </div>
            </header>
            <main class="p-8 flex-1 overflow-y-auto">
                @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded shadow-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
                @endif
                @if($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded shadow-sm">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fa-solid fa-circle-xmark text-rose-500"></i>
                        <span class="font-bold">Gagal memproses data:</span>
                    </div>
                    <ul class="list-disc pl-5 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>