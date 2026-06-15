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
    </style>
</head>
<body class="bg-white text-ink font-sans antialiased flex flex-col min-h-screen">
    <!-- Top Navigation -->
    <header class="h-[80px] bg-white border-b border-hairline flex items-center justify-between px-6 lg:px-10 sticky top-0 z-40">
        <!-- Logo -->
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-hospital text-primary text-2xl"></i>
            <span class="font-bold text-lg text-primary tracking-tight hidden md:block">Puskesmas<span class="font-normal text-ink">Ops</span></span>
        </div>

        <!-- Center Tabs -->
        <nav class="hidden md:flex gap-8">
            <a href="{{ route('dashboard') }}" class="relative text-[16px] {{ request()->routeIs('dashboard') ? 'font-semibold text-ink' : 'font-medium text-muted hover:text-ink transition-colors' }}">
                Dashboard
                @if(request()->routeIs('dashboard'))
                    <span class="absolute -bottom-[29px] left-0 right-0 h-[2px] bg-ink"></span>
                @endif
            </a>
            <a href="{{ route('items.index') }}" class="relative text-[16px] {{ request()->routeIs('items.*') ? 'font-semibold text-ink' : 'font-medium text-muted hover:text-ink transition-colors' }}">
                Inventaris
                @if(request()->routeIs('items.*'))
                    <span class="absolute -bottom-[29px] left-0 right-0 h-[2px] bg-ink"></span>
                @endif
            </a>
            <a href="{{ route('borrowings.index') }}" class="relative text-[16px] {{ request()->routeIs('borrowings.*') ? 'font-semibold text-ink' : 'font-medium text-muted hover:text-ink transition-colors' }}">
                Mutasi Aset
                @if(request()->routeIs('borrowings.*'))
                    <span class="absolute -bottom-[29px] left-0 right-0 h-[2px] bg-ink"></span>
                @endif
            </a>
            <a href="{{ route('reports.index') }}" class="relative text-[16px] {{ request()->routeIs('reports.*') ? 'font-semibold text-ink' : 'font-medium text-muted hover:text-ink transition-colors' }}">
                Laporan
                @if(request()->routeIs('reports.*'))
                    <span class="absolute -bottom-[29px] left-0 right-0 h-[2px] bg-ink"></span>
                @endif
            </a>
            @if(Auth::user()->hasRole('admin'))
            <a href="{{ route('rooms.index') }}" class="relative text-[16px] {{ request()->routeIs('rooms.*') ? 'font-semibold text-ink' : 'font-medium text-muted hover:text-ink transition-colors' }}">
                Ruangan
                @if(request()->routeIs('rooms.*'))
                    <span class="absolute -bottom-[29px] left-0 right-0 h-[2px] bg-ink"></span>
                @endif
            </a>
            @endif
        </nav>

        <!-- Right Utilities -->
        <div class="flex items-center gap-4">
            <div class="hidden lg:block text-sm font-medium text-ink px-4 py-2 hover:bg-surface-soft rounded-full transition cursor-default">
                {{ now()->translatedFormat('d M Y') }}
            </div>
            
            <div class="border border-hairline rounded-full flex items-center gap-3 p-1 pl-3 bg-white hover:airbnb-shadow transition-shadow cursor-pointer group">
                <i class="fa-solid fa-bars text-ink text-sm"></i>
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-xs font-bold text-white relative">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    <!-- Dropdown Content (Normally JS handled, but we'll show on group-hover for simplicity) -->
                    <div class="absolute right-0 top-10 w-48 bg-white border border-hairline rounded-xl airbnb-shadow hidden group-hover:block z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-hairline">
                            <p class="font-semibold text-ink text-sm capitalize">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-muted mt-0.5 capitalize">{{ Auth::user()->role->display_name }}</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button class="w-full text-left px-4 py-3 text-sm text-ink hover:bg-surface-soft transition">Log out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1 max-w-[1280px] w-full mx-auto px-6 lg:px-10 py-10">
        @if(session('success'))
        <div class="mb-8 p-4 bg-surface-soft border border-hairline rounded-xl flex items-center gap-3 text-ink text-sm">
            <i class="fa-solid fa-circle-check text-primary text-lg"></i>
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

        <!-- Mobile Navigation (visible only on small screens) -->
        <nav class="md:hidden flex gap-4 overflow-x-auto pb-4 mb-6 scrollbar-hide text-sm whitespace-nowrap">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'font-bold text-ink' : 'text-muted' }}">Dashboard</a>
            <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.*') ? 'font-bold text-ink' : 'text-muted' }}">Inventaris</a>
            <a href="{{ route('borrowings.index') }}" class="{{ request()->routeIs('borrowings.*') ? 'font-bold text-ink' : 'text-muted' }}">Mutasi</a>
            <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'font-bold text-ink' : 'text-muted' }}">Laporan</a>
            @if(Auth::user()->hasRole('admin'))
            <a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('rooms.*') ? 'font-bold text-ink' : 'text-muted' }}">Ruangan</a>
            @endif
        </nav>

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-hairline mt-auto bg-surface-soft px-6 lg:px-10 py-6">
        <div class="max-w-[1280px] mx-auto flex flex-col md:flex-row justify-between items-center gap-4 text-[14px] text-muted">
            <p>&copy; {{ date('Y') }} Sistem Inventaris Puskesmas. Terinspirasi oleh Airbnb.</p>
            <div class="flex items-center gap-4">
                <a href="#" class="hover:underline">Bantuan</a>
                <a href="#" class="hover:underline">Privasi</a>
            </div>
        </div>
    </footer>
</body>
</html>