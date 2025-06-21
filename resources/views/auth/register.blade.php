@extends('layouts.auth')

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-md">
        <div class="text-center mb-5">
            <h1 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-orange-300 to-orange-600">Sistem Manajemen Pegawai</h1>
        </div>
        
        <div class="bg-gray-800/60 backdrop-blur-sm rounded-3xl p-8 shadow-lg border border-gray-700">
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-gradient-to-tr from-orange-500 to-orange-600 rounded-2xl mx-auto mb-4 flex items-center justify-center shadow-lg shadow-orange-500/20">
                    <i class="fas fa-user-plus text-white text-3xl"></i>
                </div>
                <h3 class="font-semibold text-xl text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-orange-600">Buat Akun Baru</h3>
                <p class="text-sm text-gray-400 mt-1">Daftar untuk mengakses sistem manajemen pegawai</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-5">
                    <label for="name" class="block text-sm text-gray-400 mb-1">Nama Lengkap</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-500"></i>
                        </div>
                        <input id="name" type="text" class="w-full rounded-xl bg-gray-800/80 border text-white py-3 pl-10 pr-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 @error('name') border-red-500 @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="John Doe">
                    </div>
                    @error('name')
                        <span class="text-red-500 text-xs mt-1 block">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="mb-5">
                    <label for="email" class="block text-sm text-gray-400 mb-1">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-500"></i>
                        </div>
                        <input id="email" type="email" class="w-full rounded-xl bg-gray-800/80 border text-white py-3 pl-10 pr-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 @error('email') border-red-500 @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="contoh@email.com">
                    </div>
                    @error('email')
                        <span class="text-red-500 text-xs mt-1 block">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="mb-5">
                    <label for="password" class="block text-sm text-gray-400 mb-1">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-500"></i>
                        </div>
                        <input id="password" type="password" class="w-full rounded-xl bg-gray-800/80 border text-white py-3 pl-10 pr-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 @error('password') border-red-500 @enderror" name="password" required autocomplete="new-password" placeholder="Min. 8 karakter">
                    </div>
                    @error('password')
                        <span class="text-red-500 text-xs mt-1 block">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password-confirm" class="block text-sm text-gray-400 mb-1">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-500"></i>
                        </div>
                        <input id="password-confirm" type="password" class="w-full rounded-xl bg-gray-800/80 border border-gray-700 text-white py-3 pl-10 pr-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi">
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white py-3 rounded-xl hover:shadow-lg hover:shadow-orange-500/20 transition-all font-medium">
                    Daftar
                </button>

                <div class="text-center mt-6 text-sm text-gray-400">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-orange-400 hover:text-orange-500">Masuk</a>
                </div>
            </form>
        </div>
        
        <div class="text-center mt-5 text-gray-500 text-sm">
            © {{ date('Y') }} Sistem Manajemen Pegawai | Versi 1.0
        </div>
    </div>
</div>
@endsection
