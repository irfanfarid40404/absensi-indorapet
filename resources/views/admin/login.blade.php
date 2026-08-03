@extends('layouts.app')

@section('title', 'Login Admin - Indorapet')

@section('content')
<div class="min-h-[65vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <!-- Neo-Brutalist Badge -->
        <span class="inline-block rounded-xl bg-[#FFE600] px-4 py-1.5 text-xs font-black text-black border-2 border-black shadow-[3px_3px_0px_0px_#000] uppercase tracking-wider mb-4">
            🔑 ADMIN AUTH
        </span>
        
        <h2 class="text-3xl font-black text-black uppercase tracking-tight">
            Masuk Panel Admin
        </h2>
        <p class="mt-2 text-xs font-extrabold text-black uppercase tracking-wide">
            Akses dashboard absensi Toko Indorapet
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-[#FFF9E6] p-8 rounded-3xl border-4 border-black shadow-[10px_10px_0px_0px_#000]">
            <form class="space-y-6 m-0" action="{{ route('admin.login.post') }}" method="POST">
                @csrf
                
                <!-- Email field -->
                <div>
                    <label for="email" class="block text-xs font-black text-black uppercase tracking-wider mb-1.5">
                        Alamat Email
                    </label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                        value="{{ old('email') }}" placeholder="admin@indorapet.com"
                        class="block w-full rounded-2xl border-3 border-black py-3 px-4 text-black font-extrabold text-sm bg-white placeholder:text-slate-400 focus:outline-none focus:shadow-[4px_4px_0px_0px_#000] transition-all">
                    @error('email')
                        <p class="mt-1.5 text-xs font-black text-[#FF4747]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password field -->
                <div>
                    <label for="password" class="block text-xs font-black text-black uppercase tracking-wider mb-1.5">
                        Kata Sandi
                    </label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        placeholder="••••••••"
                        class="block w-full rounded-2xl border-3 border-black py-3 px-4 text-black font-extrabold text-sm bg-white placeholder:text-slate-400 focus:outline-none focus:shadow-[4px_4px_0px_0px_#000] transition-all">
                    @error('password')
                        <p class="mt-1.5 text-xs font-black text-[#FF4747]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <input id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}
                            class="h-5 w-5 rounded-lg border-2 border-black text-black accent-black focus:ring-0 cursor-pointer">
                        <label for="remember" class="block text-xs font-black text-black uppercase tracking-wide cursor-pointer">
                            Ingat saya di perangkat ini
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" 
                        class="w-full flex justify-center items-center gap-2 rounded-2xl bg-[#00F0FF] py-3.5 px-4 text-sm font-black text-black uppercase tracking-wider border-3 border-black shadow-[5px_5px_0px_0px_#000] hover:bg-[#FFE600] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[7px_7px_0px_0px_#000] active:translate-x-1 active:translate-y-1 active:shadow-[2px_2px_0px_0px_#000] transition-all">
                        ⚡ Masuk Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
