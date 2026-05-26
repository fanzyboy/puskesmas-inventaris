<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - internal Puskesmas</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-linear-to-br from-slate-900 to-slate-800 h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-100">
        <div class="p-8 bg-slate-50 border-b border-slate-200 text-center">
            <div class="w-16 h-16 bg-sky-100 text-sky-600 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4 shadow-sm">
                <i class="fa-solid fa-hospital-user"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Puskesmas-Ops</h1>
            <p class="text-sm text-slate-500 mt-1">Sistem Pemantauan & Inventaris Fasilitas Internal</p>
        </div>
        <form action="{{ url('/login') }}" method="POST" class="p-8 space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Alamat Email Pegawai</label>
                <div class="relative">
                    <input type="email" name="email" required placeholder="nama@puskesmas.go.id" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 text-sm transition">
                    <i class="fa-regular fa-envelope absolute left-3.5 top-3.5 text-slate-400"></i>
                </div>
                @error('email') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-2">Kata Sandi</label>
                <div class="relative">
                    <input type="password" name="password" required placeholder="••••••••" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 text-sm transition">
                    <i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-slate-400"></i>
                </div>
            </div>
            <button type="submit" class="w-full py-3 bg-sky-600 text-white rounded-xl font-semibold shadow-lg hover:bg-sky-700 transition transform active:scale-98">
                Masuk ke Aplikasi
            </button>
        </form>
    </div>
</body>
</html>