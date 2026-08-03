@extends('layouts.app')

@section('title', 'Absensi Toko Indorapet - Neo-Brutalism')

@section('styles')
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
</style>
@endsection

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Hero / Headline Section -->
    <div class="text-center max-w-3xl mx-auto space-y-4">
        <div class="inline-flex items-center gap-2 bg-[#FFE600] text-black font-black border-3 border-black px-4 py-1.5 rounded-full shadow-[4px_4px_0px_0px_#000] text-xs uppercase tracking-wider -rotate-1">
            ⚡ SISTEM ABSENSI REAL-TIME
        </div>
        <h2 class="text-3xl sm:text-5xl font-black text-black tracking-tight leading-tight">
            Pencatatan <span class="bg-[#00F0FF] text-black px-3 py-1 border-3 border-black shadow-[4px_4px_0px_0px_#000] inline-block rotate-1 my-1">Kehadiran Harian</span>
        </h2>
        <p class="text-sm sm:text-base font-extrabold text-black max-w-xl mx-auto bg-white border-3 border-black p-3.5 rounded-2xl shadow-[4px_4px_0px_0px_#000]">
            👉 Pilih nama Anda di bawah ini, lalu tentukan tindakan <span class="underline decoration-wavy decoration-[#FF66C4] font-black">Absen Masuk</span> atau <span class="underline decoration-wavy decoration-[#54EA54] font-black">Absen Pulang</span>.
        </p>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-[#FFF9E6] border-3 border-black rounded-3xl p-6 shadow-[8px_8px_0px_0px_#000] max-w-4xl mx-auto space-y-5">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search bar -->
            <div class="relative flex-1 flex items-center">
                <div class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 flex items-center justify-center text-black">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" id="employeeSearch" placeholder="Cari nama atau ID Anda..." 
                    class="block w-full rounded-2xl border-3 border-black py-3 pl-11 pr-10 text-black font-extrabold ring-0 placeholder:text-slate-500 focus:outline-none focus:bg-white focus:shadow-[4px_4px_0px_0px_#000] text-sm bg-white transition-all">
                <button type="button" id="clearSearchBtn" class="hidden absolute right-3.5 top-1/2 -translate-y-1/2 text-black font-black hover:scale-125 transition-transform" title="Bersihkan pencarian">
                    ✕
                </button>
            </div>
            
            <!-- Department Buttons (JS Filter) -->
            <div class="flex flex-wrap gap-2.5 items-center" id="deptFilters">
                <button type="button" data-dept="ALL" class="dept-btn px-4 py-2.5 rounded-xl text-xs font-black bg-[#00F0FF] text-black border-2 border-black shadow-[3px_3px_0px_0px_#000] hover:translate-x-[-1px] hover:translate-y-[-1px] transition-all">
                    SEMUA DEPARTEMEN
                </button>
                @php
                    $departments = $employees->pluck('departemen')->unique()->sort();
                @endphp
                @foreach($departments as $dept)
                    <button type="button" data-dept="{{ $dept }}" class="dept-btn px-4 py-2.5 rounded-xl text-xs font-black bg-white text-black border-2 border-black hover:bg-[#FFE600] hover:shadow-[3px_3px_0px_0px_#000] transition-all">
                        {{ strtoupper($dept) }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Employee Grid -->
    <div class="max-w-4xl mx-auto">
        <div id="employeeGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-5">
            @php
                $colors = ['bg-[#FFE600]', 'bg-[#00F0FF]', 'bg-[#FF66C4]', 'bg-[#54EA54]', 'bg-[#A259FF]', 'bg-[#FF914D]'];
            @endphp
            @forelse($employees as $index => $employee)
                @php
                    $avatarBg = $colors[$index % count($colors)];
                @endphp
                <button type="button" 
                    data-id="{{ $employee->id }}" 
                    data-name="{{ $employee->nama }}" 
                    data-code="{{ $employee->kode_karyawan }}"
                    data-dept="{{ $employee->departemen }}"
                    class="employee-card text-left p-5 bg-white rounded-2xl border-3 border-black shadow-[5px_5px_0px_0px_#000] hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[8px_8px_0px_0px_#000] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[2px_2px_0px_0px_#000] transition-all flex flex-col justify-between space-y-4 relative group cursor-pointer">
                    
                    <div class="flex items-center justify-between">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl {{ $avatarBg }} text-black font-black text-sm border-2 border-black shadow-[2px_2px_0px_0px_#000] uppercase">
                            {{ substr($employee->nama, 0, 2) }}
                        </div>
                        <span class="inline-flex items-center rounded-lg bg-[#FF66C4] text-black border-2 border-black px-2.5 py-1 text-[10px] font-black shadow-[2px_2px_0px_0px_#000] uppercase">
                            {{ $employee->departemen }}
                        </span>
                    </div>
                    
                    <div>
                        <h3 class="font-black text-black group-hover:underline text-sm line-clamp-1 leading-snug">
                            {{ $employee->nama }}
                        </h3>
                        <p class="text-xs text-black font-mono font-bold mt-1 bg-[#FFF9E6] border border-black px-2 py-0.5 rounded shadow-[1px_1px_0px_0px_#000] inline-block">
                            ID: {{ $employee->kode_karyawan }}
                        </p>
                    </div>

                    <div class="pt-3 border-t-2 border-black flex items-center justify-between">
                        <span class="bg-black text-white group-hover:bg-[#00F0FF] group-hover:text-black border border-black font-black text-[10px] px-2 py-0.5 rounded uppercase tracking-wider transition-colors shadow-[1px_1px_0px_0px_#000]">
                            TAP ABSEN
                        </span>
                        <span class="font-black text-black group-hover:translate-x-1 transition-transform">➔</span>
                    </div>
                </button>
            @empty
                <div class="col-span-full py-12 text-center bg-white border-3 border-black rounded-3xl shadow-[6px_6px_0px_0px_#000]">
                    <div class="mx-auto h-16 w-16 bg-[#FF4747] text-white border-3 border-black rounded-2xl flex items-center justify-center font-black text-2xl shadow-[3px_3px_0px_0px_#000] mb-3">
                        !
                    </div>
                    <h3 class="text-base font-black text-black uppercase">Karyawan Tidak Ditemukan</h3>
                    <p class="mt-1 text-xs font-bold text-slate-700">Silakan hubungi admin untuk menambahkan data Anda.</p>
                </div>
            @endforelse

            <!-- Dynamic JS No Results Message -->
            <div id="noResultsMsg" class="hidden col-span-full py-12 text-center bg-white border-3 border-black rounded-3xl shadow-[6px_6px_0px_0px_#000]">
                <div class="mx-auto h-16 w-16 bg-[#FF66C4] text-black border-3 border-black rounded-2xl flex items-center justify-center font-black text-2xl shadow-[3px_3px_0px_0px_#000] mb-3">
                    🔍
                </div>
                <h3 class="text-base font-black text-black uppercase">Hasil Tidak Ditemukan</h3>
                <p class="mt-1 text-xs font-bold text-slate-700">Tidak ada karyawan yang cocok dengan kata kunci pencarian Anda.</p>
            </div>
        </div>
    </div>
</div>

<!-- Absen Action Modal (Neo-Brutalism design) -->
<div id="actionModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/70 backdrop-blur-xs animate-fade-in" aria-modal="true" role="dialog">
    <div class="relative w-full max-w-sm sm:max-w-md transform overflow-hidden rounded-3xl bg-[#FFFDF5] p-5 sm:p-8 shadow-[10px_10px_0px_0px_#000] border-4 border-black flex flex-col space-y-5">
        
        <!-- Modal Close Button -->
        <button type="button" id="closeModalBtn" class="absolute top-4 right-4 h-8 w-8 sm:h-9 sm:w-9 rounded-xl bg-[#FF4747] text-white border-2 border-black font-black shadow-[2px_2px_0px_0px_#000] hover:translate-x-[-1px] hover:translate-y-[-1px] active:translate-x-[1px] active:translate-y-[1px] active:shadow-[1px_1px_0px_0px_#000] flex items-center justify-center text-sm sm:text-base transition-all" title="Tutup">
            ✕
        </button>

        <!-- Modal Header -->
        <div class="text-center space-y-2 mt-1">
            <div class="mx-auto flex h-14 w-14 sm:h-16 sm:w-16 items-center justify-center rounded-2xl bg-[#FFE600] text-black font-black text-xl sm:text-2xl border-3 border-black shadow-[4px_4px_0px_0px_#000] uppercase" id="modalInitials">
                --
            </div>
            <div>
                <h3 class="text-lg sm:text-xl font-black text-black line-clamp-1" id="modalEmployeeName">Nama Karyawan</h3>
                <span class="inline-block mt-1 bg-[#00F0FF] text-black border-2 border-black font-black text-[11px] sm:text-xs px-2.5 py-0.5 rounded-lg shadow-[2px_2px_0px_0px_#000] uppercase" id="modalEmployeeDept">DEPARTEMEN</span>
            </div>
        </div>

        <!-- Modal Content / Forms -->
        <div class="grid grid-cols-2 gap-3 sm:gap-4 pt-1">
            <!-- Form Absen Masuk -->
            <form method="POST" action="{{ route('absen.masuk') }}" class="m-0">
                @csrf
                <input type="hidden" name="employee_id" class="employee-id-input modal-employee-id">
                <button type="submit" class="w-full flex flex-col items-center justify-center gap-2 sm:gap-3 p-3.5 sm:p-5 rounded-2xl border-3 border-black bg-[#54EA54] hover:bg-[#34D399] text-black shadow-[4px_4px_0px_0px_#000] hover:shadow-[6px_6px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-[1px_1px_0px_0px_#000] transition-all cursor-pointer group">
                    <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-xl bg-black text-[#54EA54] border-2 border-black shadow-[2px_2px_0px_0px_#000]">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                    </div>
                    <span class="text-[11px] sm:text-xs font-black uppercase tracking-wider text-black text-center">Absen Masuk ➔</span>
                </button>
            </form>

            <!-- Form Absen Pulang -->
            <form method="POST" action="{{ route('absen.pulang') }}" class="m-0">
                @csrf
                <input type="hidden" name="employee_id" class="employee-id-input modal-employee-id">
                <button type="submit" class="w-full flex flex-col items-center justify-center gap-2 sm:gap-3 p-3.5 sm:p-5 rounded-2xl border-3 border-black bg-[#FF66C4] hover:bg-[#F472B6] text-black shadow-[4px_4px_0px_0px_#000] hover:shadow-[6px_6px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-[1px_1px_0px_0px_#000] transition-all cursor-pointer group">
                    <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-xl bg-black text-[#FF66C4] border-2 border-black shadow-[2px_2px_0px_0px_#000]">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3.007-3H21m-3-3l3 3m0 0l-3 3" />
                        </svg>
                    </div>
                    <span class="text-[11px] sm:text-xs font-black uppercase tracking-wider text-black text-center">Absen Pulang ➔</span>
                </button>
            </form>
        </div>
    </div>
</div>

    <!-- Floating Welcome Doll Trigger Button -->
    <button type="button" onclick="openWelcomeDollModal()" 
        class="fixed bottom-6 right-6 z-40 rounded-2xl bg-[#FF66C4] border-3 border-black text-black font-black px-4 py-3 shadow-[5px_5px_0px_0px_#000] hover:bg-[#FFE600] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[7px_7px_0px_0px_#000] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[2px_2px_0px_0px_#000] transition-all flex items-center gap-2 uppercase text-xs tracking-wider">
        <span class="text-lg">🧸</span> Hai Cantik! ✨
    </button>

    <!-- Welcome Doll Popup Modal -->
    <div id="welcomeDollModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in" aria-modal="true" role="dialog">
        <div class="w-full max-w-md transform overflow-hidden rounded-3xl bg-[#FFF9E6] p-6 sm:p-8 shadow-[12px_12px_0px_0px_#000] border-4 border-black flex flex-col items-center text-center space-y-5 relative">
            
            <!-- Close Button -->
            <button type="button" onclick="closeWelcomeDollModal()" class="absolute top-4 right-4 p-1.5 rounded-xl bg-white border-2 border-black text-black hover:bg-[#FF4747] hover:text-white shadow-[2px_2px_0px_0px_#000] transition-all" title="Tutup">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>

            <!-- Top Neo-Brutalist Badge -->
            <span class="inline-block rounded-xl bg-[#FF66C4] px-4 py-1.5 text-xs font-black text-black border-2 border-black shadow-[3px_3px_0px_0px_#000] uppercase tracking-wider">
                🎀 WELCOME TEAM CANTIK INDORAPET 💕
            </span>

            <!-- Plushie Bear Doll Illustration -->
            <div class="relative my-1">
                <svg class="h-32 w-32 mx-auto drop-shadow-[4px_4px_0px_#000]" viewBox="0 0 160 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Bear Ears -->
                    <circle cx="45" cy="45" r="22" fill="#F4A261" stroke="#000" stroke-width="4"/>
                    <circle cx="45" cy="45" r="12" fill="#E76F51" stroke="#000" stroke-width="3"/>
                    <circle cx="115" cy="45" r="22" fill="#F4A261" stroke="#000" stroke-width="4"/>
                    <circle cx="115" cy="45" r="12" fill="#E76F51" stroke="#000" stroke-width="3"/>
                    <!-- Head -->
                    <circle cx="80" cy="80" r="48" fill="#F4A261" stroke="#000" stroke-width="4"/>
                    <!-- Muzzle -->
                    <ellipse cx="80" cy="88" rx="20" ry="15" fill="#FFF3BF" stroke="#000" stroke-width="3"/>
                    <!-- Nose -->
                    <ellipse cx="80" cy="81" rx="6" ry="4" fill="#000"/>
                    <!-- Mouth -->
                    <path d="M74 88 C74 93 80 95 80 88 C80 95 86 93 86 88" stroke="#000" stroke-width="3" stroke-linecap="round" fill="none"/>
                    <!-- Eyes with Sparkles -->
                    <circle cx="60" cy="72" r="7" fill="#000"/>
                    <circle cx="58" cy="70" r="2.5" fill="#FFF"/>
                    <circle cx="100" cy="72" r="7" fill="#000"/>
                    <circle cx="98" cy="70" r="2.5" fill="#FFF"/>
                    <!-- Blushing Pink Cheeks -->
                    <ellipse cx="50" cy="85" rx="8" ry="5" fill="#FF66C4" opacity="0.8"/>
                    <ellipse cx="110" cy="85" rx="8" ry="5" fill="#FF66C4" opacity="0.8"/>
                    <!-- Pink Ribbon Bow on Ear -->
                    <path d="M30 28 C20 20 20 36 30 32 C40 36 40 20 30 28" fill="#FF66C4" stroke="#000" stroke-width="3"/>
                    <circle cx="30" cy="28" r="4" fill="#FFE600" stroke="#000" stroke-width="2"/>
                    <!-- Sparkles & Hearts -->
                    <path d="M135 25 L138 32 L145 35 L138 38 L135 45 L132 38 L125 35 L132 32 Z" fill="#FFE600" stroke="#000" stroke-width="2"/>
                    <path d="M20 70 C20 65 25 60 30 65 C35 60 40 65 40 70 C40 80 30 85 30 85 C30 85 20 80 20 70 Z" fill="#FF4747" stroke="#000" stroke-width="2"/>
                </svg>
            </div>

            <div class="space-y-2">
                <h3 class="text-xl sm:text-2xl font-black text-black uppercase tracking-tight">
                    Halo Cantik! Semangat Hari Ini! 🧸✨
                </h3>
                <p class="text-xs sm:text-sm font-extrabold text-black leading-relaxed bg-white border-2 border-black p-3.5 rounded-2xl shadow-[3px_3px_0px_0px_#000]">
                    Jangan lupa absen masuk dan absen pulang ya! Tetap ceria, tetap semangat, dan jaga kesehatan selalu! 💕🌸
                </p>
            </div>

            <button type="button" onclick="closeWelcomeDollModal()"
                class="w-full flex justify-center items-center gap-2 rounded-2xl bg-[#00F0FF] py-3.5 px-6 text-xs sm:text-sm font-black text-black uppercase tracking-wider border-3 border-black shadow-[5px_5px_0px_0px_#000] hover:bg-[#FFE600] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[7px_7px_0px_0px_#000] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[2px_2px_0px_0px_#000] transition-all">
                ⚡ Siap, Absen Sekarang! ✨
            </button>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('employeeSearch');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const cards = document.querySelectorAll('.employee-card');
        const deptButtons = document.querySelectorAll('.dept-btn');
        const noResultsMsg = document.getElementById('noResultsMsg');
        
        const actionModal = document.getElementById('actionModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const modalEmployeeName = document.getElementById('modalEmployeeName');
        const modalEmployeeDept = document.getElementById('modalEmployeeDept');
        const modalInitials = document.getElementById('modalInitials');
        const employeeIdInputs = document.querySelectorAll('.modal-employee-id');

        const welcomeDollModal = document.getElementById('welcomeDollModal');

        // Welcome Doll Modal Functions
        window.openWelcomeDollModal = function() {
            welcomeDollModal.classList.remove('hidden');
            welcomeDollModal.classList.add('flex');
        };

        window.closeWelcomeDollModal = function() {
            welcomeDollModal.classList.add('hidden');
            welcomeDollModal.classList.remove('flex');
        };

        // Open Automatically on page load
        openWelcomeDollModal();

        welcomeDollModal.addEventListener('click', function(e) {
            if (e.target === welcomeDollModal) {
                closeWelcomeDollModal();
            }
        });
        
        let currentDeptFilter = 'ALL';

        // Filter Logic Function
        function filterEmployees() {
            const query = searchInput.value.toLowerCase().trim();
            
            // Show/hide clear button
            if (query.length > 0) {
                clearSearchBtn.classList.remove('hidden');
            } else {
                clearSearchBtn.classList.add('hidden');
            }

            let visibleCount = 0;

            cards.forEach(card => {
                const name = (card.getAttribute('data-name') || '').toLowerCase();
                const code = (card.getAttribute('data-code') || '').toLowerCase();
                const dept = (card.getAttribute('data-dept') || '').toLowerCase();
                
                const matchesSearch = name.includes(query) || code.includes(query) || dept.includes(query);
                const matchesDept = (currentDeptFilter === 'ALL' || card.getAttribute('data-dept') === currentDeptFilter);
                
                if (matchesSearch && matchesDept) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (noResultsMsg) {
                if (visibleCount === 0 && cards.length > 0) {
                    noResultsMsg.classList.remove('hidden');
                } else {
                    noResultsMsg.classList.add('hidden');
                }
            }
        }

        // Search Input Listener
        searchInput.addEventListener('input', filterEmployees);
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                searchInput.value = '';
                filterEmployees();
            }
        });

        // Clear Search Listener
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.focus();
            filterEmployees();
        });

        // Department Filter Click Listener
        deptButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active button styling (Neo-Brutalism style)
                deptButtons.forEach(b => {
                    b.className = 'dept-btn px-4 py-2.5 rounded-xl text-xs font-black bg-white text-black border-2 border-black hover:bg-[#FFE600] hover:shadow-[3px_3px_0px_0px_#000] transition-all';
                });
                this.className = 'dept-btn px-4 py-2.5 rounded-xl text-xs font-black bg-[#00F0FF] text-black border-2 border-black shadow-[3px_3px_0px_0px_#000] hover:translate-x-[-1px] hover:translate-y-[-1px] transition-all';
                
                currentDeptFilter = this.getAttribute('data-dept');
                filterEmployees();
            });
        });

        // Modal Open Logic
        cards.forEach(card => {
            card.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const dept = this.getAttribute('data-dept');
                
                modalEmployeeName.textContent = name;
                modalEmployeeDept.textContent = dept;
                modalInitials.textContent = name.substring(0, 2);
                
                employeeIdInputs.forEach(input => {
                    input.value = id;
                });
                
                actionModal.classList.remove('hidden');
                actionModal.classList.add('flex');
            });
        });

        // Modal Close Logic
        function closeModal() {
            actionModal.classList.add('hidden');
            actionModal.classList.remove('flex');
        }

        closeModalBtn.addEventListener('click', closeModal);
        actionModal.addEventListener('click', function(e) {
            if (e.target === actionModal) {
                closeModal();
            }
        });
    });
</script>
@endsection
