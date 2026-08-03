<!DOCTYPE html>
<html lang="id" class="h-full bg-[#FFFDF5] text-black">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Absensi Toko Indorapet')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <style>
        html, body {
            min-height: 100vh;
            background-color: #FFFDF5 !important;
            background-image: radial-gradient(#000000 1.5px, transparent 1.5px) !important;
            background-size: 24px 24px !important;
            background-attachment: fixed !important;
        }
        .border-3 { border-width: 3px; }
        .border-b-3 { border-bottom-width: 3px; }
        .border-t-3 { border-top-width: 3px; }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #FFF9E6;
            border-left: 2px solid #000;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #000;
            border-radius: 0px;
        }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen flex flex-col font-sans antialiased bg-[#FFFDF5] text-black selection:bg-[#FFE600] selection:text-black">

    <!-- Header Navigation -->
    <header class="sticky top-0 z-40 w-full border-b-3 border-black bg-[#FFE600] shadow-[0_4px_0_0_#000]">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="/" class="flex items-center gap-3 no-underline">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-black text-[#FFE600] border-2 border-black font-black text-xl shadow-[3px_3px_0px_0px_#000]">
                    ⚡
                </div>
                <div>
                    <h1 class="text-lg font-black tracking-tight text-black uppercase">Indorapet</h1>
                    <p class="text-[11px] text-black font-extrabold tracking-wide uppercase">Sistem Absensi Toko</p>
                </div>
            </a>
            
            <!-- Desktop Nav -->
            <nav class="hidden sm:flex items-center gap-3">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="rounded-xl bg-white border-2 border-black px-3.5 py-1.5 text-xs font-black text-black shadow-[3px_3px_0px_0px_#000] hover:bg-[#00F0FF] hover:translate-x-[-1px] hover:translate-y-[-1px] transition-all">Dashboard</a>
                    <a href="{{ route('admin.karyawan') }}" class="rounded-xl bg-white border-2 border-black px-3.5 py-1.5 text-xs font-black text-black shadow-[3px_3px_0px_0px_#000] hover:bg-[#FF66C4] hover:translate-x-[-1px] hover:translate-y-[-1px] transition-all">Data Karyawan</a>
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline m-0">
                        @csrf
                        <button type="submit" class="rounded-xl bg-[#FF4747] text-white border-2 border-black px-3.5 py-1.5 text-xs font-black shadow-[3px_3px_0px_0px_#000] hover:translate-x-[-1px] hover:translate-y-[-1px] transition-all">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('admin.login') }}" class="rounded-xl bg-white border-2 border-black px-3.5 py-1.5 text-xs font-black text-black shadow-[3px_3px_0px_0px_#000] hover:bg-[#00F0FF] hover:translate-x-[-1px] hover:translate-y-[-1px] transition-all">
                        🔑 Masuk Admin
                    </a>
                @endauth
            </nav>

            <!-- Hamburger Button (Mobile) -->
            <button type="button" id="hamburgerBtn" onclick="toggleMobileMenu()" class="sm:hidden p-2 rounded-xl text-black border-2 border-black bg-white shadow-[2px_2px_0px_0px_#000] transition-colors">
                <svg id="hamburgerIcon" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg id="closeIcon" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Menu Panel -->
        <div id="mobileMenu" class="hidden sm:hidden border-t-3 border-black bg-[#FFE600]">
            <div class="px-4 py-4 space-y-2">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-xl px-4 py-3 text-sm font-black bg-white border-2 border-black text-black shadow-[3px_3px_0px_0px_#000]">
                        Dashboard Admin
                    </a>
                    <a href="{{ route('admin.karyawan') }}" class="block rounded-xl px-4 py-3 text-sm font-black bg-white border-2 border-black text-black shadow-[3px_3px_0px_0px_#000]">
                        Data Karyawan
                    </a>
                    <div class="pt-2 mt-2">
                        <form method="POST" action="{{ route('admin.logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="w-full text-left rounded-xl px-4 py-3 text-sm font-black bg-[#FF4747] text-white border-2 border-black shadow-[3px_3px_0px_0px_#000]">
                                Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('admin.login') }}" class="block rounded-xl px-4 py-3 text-sm font-black bg-white border-2 border-black text-black shadow-[3px_3px_0px_0px_#000]">
                        🔑 Masuk Admin
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Toast / Alert Notification Banner -->
            @if(session('success'))
                <div class="mb-8 rounded-2xl bg-[#54EA54] border-3 border-black p-5 shadow-[6px_6px_0px_0px_#000] animate-fade-in flash-alert">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-black text-[#54EA54] font-black text-lg border border-black shadow-[2px_2px_0px_0px_#000]">
                            ✓
                        </div>
                        <p class="text-sm font-black text-black tracking-wide">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-8 rounded-2xl bg-[#FF4747] border-3 border-black p-5 shadow-[6px_6px_0px_0px_#000] animate-fade-in flash-alert">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-black text-[#FF4747] font-black text-lg border border-black shadow-[2px_2px_0px_0px_#000]">
                            ✕
                        </div>
                        <p class="text-sm font-black text-white tracking-wide">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t-3 border-black bg-[#FFE600] py-6 shadow-[0_-4px_0_0_#000]">
        <div class="mx-auto max-w-7xl px-4 text-center text-xs font-black text-black uppercase tracking-wider sm:px-6 lg:px-8">
            &copy; {{ date('Y') }} Toko Indorapet. All rights reserved.
        </div>
    </footer>

    @yield('scripts')
    <script>
        // Mobile hamburger menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const hamburgerIcon = document.getElementById('hamburgerIcon');
            const closeIcon = document.getElementById('closeIcon');
            
            menu.classList.toggle('hidden');
            hamburgerIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        }

        // Auto-dismiss alert notifications after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.flash-alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>

    @yield('scripts')
    <script>
        // Mobile hamburger menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const hamburgerIcon = document.getElementById('hamburgerIcon');
            const closeIcon = document.getElementById('closeIcon');
            
            menu.classList.toggle('hidden');
            hamburgerIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        }

        // Auto-dismiss alert notifications after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.flash-alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
