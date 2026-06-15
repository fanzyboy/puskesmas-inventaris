<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Internal Puskesmas</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #222222;
        }
    </style>
</head>
<body class="bg-[#ffffff] h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-[450px] bg-[#ffffff] rounded-[14px] p-8 border border-[#ebebeb] shadow-[rgba(0,0,0,0.02)_0_0_0_1px,rgba(0,0,0,0.04)_0_2px_6px,rgba(0,0,0,0.1)_0_4px_8px]">
        
        <div class="mb-8">
            <div class="w-12 h-12 bg-[#ff385c] text-white rounded-full flex items-center justify-center text-xl mb-4">
                <i class="fa-solid fa-hospital-user"></i>
            </div>
            <h1 class="text-[22px] font-medium text-[#222222] tracking-tight leading-[1.18]">Selamat datang kembali</h1>
            <p class="text-sm text-[#6a6a6a] mt-2">Sistem Pemantauan & Inventaris Fasilitas Internal Puskesmas</p>
        </div>

        <form action="{{ url('/login') }}" method="POST" class="space-y-5">
            @csrf
            
            <div class="relative group">
                <div class="border border-[#dddddd] rounded-[8px] focus-within:border-[#222222] focus-within:ring-0 transition duration-200">
                    <label class="block text-[12px] font-bold text-[#6a6a6a] uppercase px-3 pt-2.5 tracking-wide">Alamat Email Pegawai</label>
                    <input type="email" name="email" required placeholder="nama@puskesmas.go.id" 
                           class="w-full px-3 pb-2.5 pt-0.5 bg-transparent border-none text-[#222222] text-sm focus:outline-none placeholder-[#929292]">
                </div>
                @error('email') 
                    <span class="text-xs text-[#c13515] font-medium mt-1.5 block">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}
                    </span> 
                @enderror
            </div>

            <div class="relative group">
                <div class="border border-[#dddddd] rounded-[8px] focus-within:border-[#222222] focus-within:ring-0 transition duration-200">
                    <label class="block text-[12px] font-bold text-[#6a6a6a] uppercase px-3 pt-2.5 tracking-wide">Kata Sandi</label>
                    <input type="password" name="password" required placeholder="••••••••" 
                           class="w-full px-3 pb-2.5 pt-0.5 bg-transparent border-none text-[#222222] text-sm focus:outline-none placeholder-[#929292]">
                </div>
            </div>

            <button type="submit" 
                    class="w-full h-[48px] bg-[#ff385c] hover:bg-[#e00b41] active:scale-[0.99] text-white rounded-[8px] text-[16px] font-medium transition-all duration-200 flex items-center justify-center shadow-sm cursor-pointer">
                Masuk ke Aplikasi
            </button>
        </form>
    </div>
</body>
</html>