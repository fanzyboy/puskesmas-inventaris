<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris Puskesmas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style type="text/tailwindcss">
        @theme {
            --color-primary: #ff385c;
            --color-primary-hover: #e00b41;
            --color-ink: #222222;
            --color-body: #3f3f3f;
            --color-muted: #6a6a6a;
            --color-hairline: #dddddd;
            --color-surface-soft: #f7f7f7;
            --font-sans: 'Inter', -apple-system, system-ui, Roboto, sans-serif;
        }
        
        .airbnb-shadow {
            box-shadow: rgba(0, 0, 0, 0.02) 0 0 0 1px, rgba(0, 0, 0, 0.04) 0 2px 6px 0, rgba(0, 0, 0, 0.1) 0 4px 8px 0;
        }

        @keyframes fadeInSlideUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: none;
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .hover-lift {
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-white text-ink font-sans antialiased flex h-screen overflow-hidden">
    <!-- Sidebar (Desktop) -->
    <aside class="w-[260px] flex-shrink-0 bg-white border-r border-hairline flex flex-col hidden md:flex h-full z-40 relative">
        <div class="h-[80px] flex items-center px-6 border-b border-hairline shrink-0">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-hospital text-primary text-2xl"></i>
                <span class="font-bold text-lg text-primary tracking-tight">Puskesmas<span class="font-normal text-ink">Ops</span></span>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-4 flex flex-col gap-2">
            <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-primary text-white font-medium airbnb-shadow scale-[1.02]' : 'text-muted hover:bg-surface-soft hover:text-ink hover:translate-x-1 font-medium' }}">
                <i class="fa-solid fa-chart-line w-5 text-center {{ request()->routeIs('dashboard') ? '' : 'group-hover:text-primary transition-colors' }}"></i>
                Dashboard
            </a>
            <a href="{{ route('items.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('items.*') ? 'bg-primary text-white font-medium airbnb-shadow scale-[1.02]' : 'text-muted hover:bg-surface-soft hover:text-ink hover:translate-x-1 font-medium' }}">
                <i class="fa-solid fa-boxes-stacked w-5 text-center {{ request()->routeIs('items.*') ? '' : 'group-hover:text-primary transition-colors' }}"></i>
                Inventaris
            </a>
            <a href="{{ route('borrowings.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('borrowings.*') ? 'bg-primary text-white font-medium airbnb-shadow scale-[1.02]' : 'text-muted hover:bg-surface-soft hover:text-ink hover:translate-x-1 font-medium' }}">
                <i class="fa-solid fa-hand-holding-hand w-5 text-center {{ request()->routeIs('borrowings.*') ? '' : 'group-hover:text-primary transition-colors' }}"></i>
                Mutasi Aset
            </a>
            <a href="{{ route('reports.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('reports.*') ? 'bg-primary text-white font-medium airbnb-shadow scale-[1.02]' : 'text-muted hover:bg-surface-soft hover:text-ink hover:translate-x-1 font-medium' }}">
                <i class="fa-solid fa-file-contract w-5 text-center {{ request()->routeIs('reports.*') ? '' : 'group-hover:text-primary transition-colors' }}"></i>
                Laporan
            </a>
            @if(Auth::user()->hasRole('admin'))
            <a href="{{ route('rooms.index') }}" class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('rooms.*') ? 'bg-primary text-white font-medium airbnb-shadow scale-[1.02]' : 'text-muted hover:bg-surface-soft hover:text-ink hover:translate-x-1 font-medium' }}">
                <i class="fa-solid fa-door-open w-5 text-center {{ request()->routeIs('rooms.*') ? '' : 'group-hover:text-primary transition-colors' }}"></i>
                Ruangan
            </a>
            @endif
        </nav>

        <div class="p-4 border-t border-hairline flex flex-col gap-4 shrink-0">
            @if(Auth::check() && !Auth::user()->hasRole('admin') && Auth::user()->rooms && Auth::user()->rooms->count() > 0)
                <form action="{{ route('switch.room') }}" method="POST" class="m-0">
                    @csrf
                    <div class="relative group">
                        <select name="room_id" onchange="this.form.submit()" class="w-full appearance-none bg-surface-soft border border-hairline text-ink text-sm font-medium rounded-xl pl-4 pr-9 py-2.5 hover:airbnb-shadow transition cursor-pointer focus:outline-none focus:border-primary truncate">
                            @foreach(Auth::user()->rooms as $room)
                                <option value="{{ $room->id }}" {{ session('active_room_id', Auth::user()->room_id) == $room->id ? 'selected' : '' }}>
                                    {{ $room->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-muted group-hover:text-ink transition-colors">
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </form>
            @endif

            <div class="flex items-center gap-3 px-2 hover:bg-surface-soft p-2 rounded-xl transition-colors cursor-pointer group">
                <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-sm font-bold text-white shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-ink text-sm capitalize truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-muted mt-0.5 capitalize truncate">{{ Auth::user()->role->display_name ?? 'Petugas' }}</p>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="m-0 mt-1">
                @csrf
                <button class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-rose-500 hover:bg-rose-50 hover:text-rose-600 transition-all duration-300 hover:translate-x-1 font-medium text-sm">
                    <i class="fa-solid fa-arrow-right-from-bracket w-5 text-left"></i>
                    Log out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col h-full overflow-hidden relative bg-surface-soft md:bg-white">
        
        <!-- Mobile Header -->
        <header class="md:hidden h-[70px] bg-white border-b border-hairline flex items-center justify-between px-6 shrink-0 z-40 relative">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-hospital text-primary text-xl"></i>
                <span class="font-bold text-lg text-primary tracking-tight">Puskesmas<span class="font-normal text-ink">Ops</span></span>
            </div>
            <button id="mobile-menu-btn" class="w-10 h-10 rounded-full bg-surface-soft flex items-center justify-center text-ink hover:bg-gray-200 transition">
                <i class="fa-solid fa-bars"></i>
            </button>
        </header>

        <!-- Mobile Navigation (hidden by default) -->
        <div id="mobile-nav" class="md:hidden hidden absolute top-[70px] left-0 right-0 bg-white border-b border-hairline airbnb-shadow z-30 flex flex-col">
            <nav class="flex flex-col gap-1 p-4">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-primary text-white font-medium' : 'text-muted hover:bg-surface-soft font-medium' }}">
                    <i class="fa-solid fa-chart-line w-5 text-center"></i> Dashboard
                </a>
                <a href="{{ route('items.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('items.*') ? 'bg-primary text-white font-medium' : 'text-muted hover:bg-surface-soft font-medium' }}">
                    <i class="fa-solid fa-boxes-stacked w-5 text-center"></i> Inventaris
                </a>
                <a href="{{ route('borrowings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('borrowings.*') ? 'bg-primary text-white font-medium' : 'text-muted hover:bg-surface-soft font-medium' }}">
                    <i class="fa-solid fa-hand-holding-hand w-5 text-center"></i> Mutasi Aset
                </a>
                <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('reports.*') ? 'bg-primary text-white font-medium' : 'text-muted hover:bg-surface-soft font-medium' }}">
                    <i class="fa-solid fa-file-contract w-5 text-center"></i> Laporan
                </a>
                @if(Auth::user()->hasRole('admin'))
                <a href="{{ route('rooms.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('rooms.*') ? 'bg-primary text-white font-medium' : 'text-muted hover:bg-surface-soft font-medium' }}">
                    <i class="fa-solid fa-door-open w-5 text-center"></i> Ruangan
                </a>
                @endif
            </nav>
            <div class="p-4 border-t border-hairline bg-surface-soft">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-sm font-bold text-white">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-ink text-sm capitalize">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-muted capitalize">{{ Auth::user()->role->display_name ?? 'Petugas' }}</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button class="w-10 h-10 rounded-full bg-white text-rose-500 flex items-center justify-center border border-hairline hover:bg-rose-50 transition airbnb-shadow-sm">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
                @if(Auth::check() && !Auth::user()->hasRole('admin') && Auth::user()->rooms && Auth::user()->rooms->count() > 0)
                    <form action="{{ route('switch.room') }}" method="POST" class="m-0">
                        @csrf
                        <div class="relative group">
                            <select name="room_id" onchange="this.form.submit()" class="w-full appearance-none bg-white border border-hairline text-ink text-sm font-medium rounded-xl pl-4 pr-9 py-2 hover:airbnb-shadow transition cursor-pointer focus:outline-none focus:border-primary truncate">
                                @foreach(Auth::user()->rooms as $room)
                                    <option value="{{ $room->id }}" {{ session('active_room_id', Auth::user()->room_id) == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-muted">
                                <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <main class="flex-1 overflow-y-auto w-full bg-surface-soft md:bg-white md:rounded-tl-2xl md:border-t md:border-l md:border-hairline shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)]">
            <div class="max-w-[1200px] mx-auto px-6 lg:px-10 py-8 lg:py-10">
                
                <div class="flex justify-between items-center mb-6 lg:mb-8 hover-lift">
                    <div class="text-sm font-medium text-ink bg-white border border-hairline px-4 py-2 rounded-full inline-flex items-center gap-2 airbnb-shadow-sm">
                        <i class="fa-regular fa-calendar-days text-primary"></i> 
                        {{ now()->translatedFormat('d F Y') }}
                    </div>
                </div>

                @if(session('success'))
                <div class="mb-8 p-4 bg-white border border-hairline rounded-xl flex items-center gap-3 text-ink text-sm airbnb-shadow-sm">
                    <div class="w-8 h-8 rounded-full bg-green-50 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-check text-green-600"></i>
                    </div>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
                @endif
                
                @if($errors->any())
                <div class="mb-8 p-4 bg-rose-50 border border-rose-200 rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-circle-exclamation text-primary"></i>
                        <span class="font-semibold text-ink text-sm">Harap periksa kembali isian Anda:</span>
                    </div>
                    <ul class="list-disc pl-5 text-sm text-body space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @yield('content')
            </div>

            <footer class="mt-auto px-6 lg:px-10 py-8">
                <div class="max-w-[1200px] mx-auto flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-muted">
                    <p>&copy; {{ date('Y') }} Sistem Inventaris Puskesmas.</p>
                    <div class="flex items-center gap-4">
                        <a href="#" class="hover:text-ink transition">Bantuan</a>
                        <a href="#" class="hover:text-ink transition">Privasi</a>
                    </div>
                </div>
            </footer>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('mobile-menu-btn');
            const nav = document.getElementById('mobile-nav');
            
            if(btn && nav) {
                btn.addEventListener('click', function() {
                    nav.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>
</html>