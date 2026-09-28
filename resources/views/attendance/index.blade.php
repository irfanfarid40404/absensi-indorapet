@extends('layouts.app')

@section('title', 'Presensi Karyawan - PT Indorapet')

@section('content')
<div class="space-y-6">

    <!-- Top Hero Banner with Live Clock & Quick Stats -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            
            <!-- Left Info -->
            <div class="space-y-1">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
                    Terminal Presensi Terbuka
                </div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Presensi Mandiri Karyawan</h1>
                <p class="text-xs text-slate-500">Cari nama atau ID Anda, lalu tekan tombol untuk mencatat jam masuk atau pulang.</p>
            </div>

            <!-- Right: Real-time Live Clock Card -->
            <div class="flex items-center gap-4 bg-slate-50 border border-slate-200/70 rounded-xl px-5 py-3 self-start md:self-auto">
                <div class="p-2 bg-indigo-600 text-white rounded-lg shadow-xs">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div id="liveClock" class="font-mono text-2xl font-bold tracking-tight text-slate-900">--:--:--</div>
                    <div id="liveDate" class="text-xs font-medium text-slate-500 capitalize">Memuat tanggal...</div>
                </div>
            </div>

        </div>

        <!-- Quick Metrics Overview -->
        @php
            $totalActive = $employees->count();
            $clockedInCount = $todayAttendances->count();
            $completedCount = $todayAttendances->whereNotNull('jam_keluar')->count();
            $notYetCount = max(0, $totalActive - $clockedInCount);
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-6 mt-6 border-t border-slate-100">
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/60">
                <span class="text-[11px] font-medium text-slate-500">Total Karyawan Aktif</span>
                <p class="text-xl font-bold text-slate-900 mt-0.5">{{ $totalActive }}</p>
            </div>
            <div class="p-3 bg-emerald-50/60 rounded-xl border border-emerald-200/60">
                <span class="text-[11px] font-medium text-emerald-700">Sudah Hadir Hari Ini</span>
                <p class="text-xl font-bold text-emerald-800 mt-0.5">{{ $clockedInCount }}</p>
            </div>
            <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-200/60">
                <span class="text-[11px] font-medium text-blue-700">Sudah Pulang</span>
                <p class="text-xl font-bold text-blue-800 mt-0.5">{{ $completedCount }}</p>
            </div>
            <div class="p-3 bg-amber-50/60 rounded-xl border border-amber-200/60">
                <span class="text-[11px] font-medium text-amber-700">Belum Presensi</span>
                <p class="text-xl font-bold text-amber-800 mt-0.5">{{ $notYetCount }}</p>
            </div>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
        
        <div class="flex flex-col md:flex-row gap-3">
            <!-- Search bar -->
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input type="text" id="employeeSearch" 
                       placeholder="Cari nama karyawan atau ID (contoh: Joko, 101)..." 
                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-9 text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all">
                <button type="button" id="clearSearchBtn" 
                        class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>

            <!-- View Layout Mode / Per Page Selector -->
            <div class="flex items-center gap-2 text-xs text-slate-500 self-end md:self-auto">
                <span>Tampilkan:</span>
                <select id="itemsPerPage" class="rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:border-indigo-500">
                    <option value="16">16 per batch</option>
                    <option value="24">24 per batch</option>
                    <option value="36">36 per batch</option>
                    <option value="999">Semua Karyawan</option>
                </select>
            </div>
        </div>

        <!-- Filter Tabs: Department -->
        <div class="flex flex-wrap items-center gap-1.5 pt-2 border-t border-slate-100" id="deptFilters">
            <span class="text-xs font-semibold text-slate-400 mr-1">Departemen:</span>
            <button type="button" data-dept="ALL" 
                    class="dept-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 text-white transition-colors">
                Semua ({{ $employees->count() }})
            </button>
            @foreach($departments as $dept)
                @php
                    $countDept = $employees->where('departemen', $dept)->count();
                @endphp
                <button type="button" data-dept="{{ $dept }}" 
                        class="dept-btn px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors">
                    {{ $dept }} ({{ $countDept }})
                </button>
            @endforeach
        </div>

    </div>

    <!-- Employee Grid (Rendered with client-side Pagination / Lazy Loading) -->
    <div class="space-y-6">
        
        <!-- Results Counter & Status Summary -->
        <div class="flex items-center justify-between text-xs text-slate-500 px-1">
            <span id="resultsCount">Menampilkan {{ min(16, $employees->count()) }} dari {{ $employees->count() }} karyawan</span>
            <span class="hidden sm:inline">Klik kartu karyawan untuk melakukan presensi</span>
        </div>

        <div id="employeeGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($employees as $employee)
                @php
                    $att = $todayAttendances->get($employee->id);
                    $hasIn = $att && !empty($att->jam_masuk);
                    $hasOut = $att && !empty($att->jam_keluar);
                    $inTime = $hasIn ? \Carbon\Carbon::parse($att->jam_masuk)->format('H:i') : null;
                    $outTime = $hasOut ? \Carbon\Carbon::parse($att->jam_keluar)->format('H:i') : null;
                    $statusClass = 'not-clocked';
                    if ($hasIn && $hasOut) {
                        $statusClass = 'completed';
                    } elseif ($hasIn) {
                        $statusClass = 'clocked-in';
                    }
                @endphp

                <div data-id="{{ $employee->id }}" 
                     data-name="{{ $employee->nama }}" 
                     data-code="{{ $employee->kode_karyawan }}"
                     data-dept="{{ $employee->departemen }}"
                     data-status="{{ $statusClass }}"
                     data-in="{{ $inTime ?? '' }}"
                     data-out="{{ $outTime ?? '' }}"
                     class="employee-card group bg-white rounded-xl border border-slate-200/80 p-4 shadow-xs hover:shadow-md hover:border-indigo-300 transition-all flex flex-col justify-between gap-3 cursor-pointer">
                    
                    <!-- Card Header: Avatar, Name, Dept Badge -->
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700 font-bold text-xs border border-indigo-100 uppercase group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            {{ substr($employee->nama, 0, 2) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-slate-900 text-sm truncate group-hover:text-indigo-600 transition-colors">
                                {{ $employee->nama }}
                            </h3>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="font-mono text-[11px] text-slate-400">ID: {{ $employee->kode_karyawan }}</span>
                                <span class="text-slate-300">•</span>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">
                                    {{ $employee->departemen }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer: Status Today & Quick Action -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        @if($hasIn && $hasOut)
                            <div class="flex items-center gap-1.5 text-blue-700 font-medium">
                                <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                                <span>Selesai ({{ $inTime }} - {{ $outTime }})</span>
                            </div>
                        @elseif($hasIn)
                            <div class="flex items-center gap-1.5 text-emerald-700 font-medium">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Masuk: {{ $inTime }}</span>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5 text-slate-400">
                                <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                                <span>Belum Presensi</span>
                            </div>
                        @endif

                        <span class="text-indigo-600 group-hover:translate-x-0.5 transition-transform font-semibold text-xs inline-flex items-center gap-0.5">
                            Absen
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </span>
                    </div>

                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-white rounded-xl border border-slate-200">
                    <svg class="mx-auto h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-slate-800">Belum ada data karyawan aktif</h3>
                    <p class="mt-1 text-xs text-slate-500">Silakan hubungi administrator untuk menambahkan karyawan.</p>
                </div>
            @endforelse

            <!-- Dynamic No Results Placeholder -->
            <div id="noResultsMsg" class="hidden col-span-full py-12 text-center bg-white rounded-xl border border-slate-200">
                <svg class="mx-auto h-9 w-9 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-slate-800">Karyawan tidak ditemukan</h3>
                <p class="mt-1 text-xs text-slate-500">Coba ubah kata kunci pencarian atau ganti filter departemen.</p>
            </div>
        </div>

        <!-- Pagination & Lazy Load Actions -->
        <div id="paginationControls" class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-200">
            <!-- Lazy Load Button -->
            <button type="button" id="loadMoreBtn" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-white border border-slate-300 px-5 py-2.5 text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-400 transition-all">
                <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
                <span>Muat Lebih Banyak (Lazy Load)</span>
            </button>

            <!-- Page Number Navigation -->
            <div class="flex items-center gap-1.5" id="pageNumberButtons">
                <!-- Injected via JavaScript -->
            </div>
        </div>

    </div>

</div>

<!-- Clean Professional Attendance Action Modal -->
<div id="actionModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity" aria-modal="true" role="dialog">
    <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl border border-slate-200 transition-all flex flex-col space-y-5">
        
        <!-- Modal Close Button -->
        <button type="button" id="closeModalBtn" 
                class="absolute top-4 right-4 p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors" title="Tutup">
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
            </svg>
        </button>

        <!-- Employee Info Header in Modal -->
        <div class="flex items-center gap-4 pr-8">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700 font-bold text-sm border border-indigo-100 uppercase" id="modalInitials">
                --
            </div>
            <div class="min-w-0">
                <h3 class="text-base font-bold text-slate-900 truncate" id="modalEmployeeName">Nama Karyawan</h3>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="font-mono text-xs text-slate-500" id="modalEmployeeCode">ID: -</span>
                    <span class="text-slate-300">•</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700" id="modalEmployeeDept">
                        Departemen
                    </span>
                </div>
            </div>
        </div>

        <!-- Today's Status Banner inside Modal -->
        <div id="modalStatusBanner" class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-center justify-between">
            <span class="font-medium text-slate-500">Status Hari Ini:</span>
            <span id="modalStatusText" class="font-semibold text-slate-800">Belum Presensi</span>
        </div>

        <!-- Actions: Absen Masuk & Absen Pulang -->
        <div class="grid grid-cols-2 gap-3 pt-2">
            
            <!-- Form Absen Masuk -->
            <form method="POST" action="{{ route('absen.masuk') }}" class="m-0">
                @csrf
                <input type="hidden" name="employee_id" class="modal-employee-id">
                <button type="submit" id="modalBtnMasuk" 
                        class="w-full flex flex-col items-center justify-center gap-2 p-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm hover:shadow transition-all disabled:opacity-50 disabled:pointer-events-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    <span>Absen Masuk</span>
                </button>
            </form>

            <!-- Form Absen Pulang -->
            <form method="POST" action="{{ route('absen.pulang') }}" class="m-0">
                @csrf
                <input type="hidden" name="employee_id" class="modal-employee-id">
                <button type="submit" id="modalBtnPulang" 
                        class="w-full flex flex-col items-center justify-center gap-2 p-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-sm hover:shadow transition-all disabled:opacity-50 disabled:pointer-events-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3.007-3H21m-3-3l3 3m0 0l-3 3" />
                    </svg>
                    <span>Absen Pulang</span>
                </button>
            </form>

        </div>

        <div class="text-center pt-1">
            <span class="text-[11px] text-slate-400">Jam standar masuk: {{ $jamMasukStandar }} WIB</span>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
    // Live Digital Clock
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        
        const clockEl = document.getElementById('liveClock');
        if (clockEl) {
            clockEl.textContent = `${hours}:${minutes}:${seconds}`;
        }
        
        const dateEl = document.getElementById('liveDate');
        if (dateEl) {
            const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
            dateEl.textContent = now.toLocaleDateString('id-ID', options);
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Client-side Lazy Load & Pagination Logic
    document.addEventListener('DOMContentLoaded', function() {
        const allCards = Array.from(document.querySelectorAll('.employee-card'));
        const searchInput = document.getElementById('employeeSearch');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const deptButtons = document.querySelectorAll('.dept-btn');
        const itemsPerPageSelect = document.getElementById('itemsPerPage');
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        const pageNumberButtons = document.getElementById('pageNumberButtons');
        const resultsCountEl = document.getElementById('resultsCount');
        const noResultsMsg = document.getElementById('noResultsMsg');
        
        // Modal elements
        const actionModal = document.getElementById('actionModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const modalInitials = document.getElementById('modalInitials');
        const modalEmployeeName = document.getElementById('modalEmployeeName');
        const modalEmployeeCode = document.getElementById('modalEmployeeCode');
        const modalEmployeeDept = document.getElementById('modalEmployeeDept');
        const modalStatusText = document.getElementById('modalStatusText');
        const modalBtnMasuk = document.getElementById('modalBtnMasuk');
        const modalBtnPulang = document.getElementById('modalBtnPulang');
        const employeeIdInputs = document.querySelectorAll('.modal-employee-id');

        let currentDept = 'ALL';
        let currentPage = 1;
        let perPage = parseInt(itemsPerPageSelect.value, 10);
        let filteredCards = [...allCards];

        function applyFilter() {
            const query = searchInput.value.toLowerCase().trim();
            clearSearchBtn.classList.toggle('hidden', query === '');

            filteredCards = allCards.filter(card => {
                const name = card.getAttribute('data-name').toLowerCase();
                const code = card.getAttribute('data-code').toLowerCase();
                const dept = card.getAttribute('data-dept');

                const matchesQuery = name.includes(query) || code.includes(query);
                const matchesDept = (currentDept === 'ALL' || dept === currentDept);

                return matchesQuery && matchesDept;
            });

            currentPage = 1;
            renderCards();
        }

        function renderCards() {
            const total = filteredCards.length;
            const maxPage = Math.ceil(total / perPage) || 1;
            if (currentPage > maxPage) currentPage = maxPage;

            // Hide all first
            allCards.forEach(card => card.classList.add('hidden'));

            if (total === 0) {
                noResultsMsg.classList.remove('hidden');
                resultsCountEl.textContent = 'Tidak ada hasil yang cocok';
                loadMoreBtn.classList.add('hidden');
                pageNumberButtons.innerHTML = '';
                return;
            }

            noResultsMsg.classList.add('hidden');

            // Show cards for the current view
            const endIndex = Math.min(currentPage * perPage, total);
            for (let i = 0; i < endIndex; i++) {
                filteredCards[i].classList.remove('hidden');
            }

            resultsCountEl.textContent = `Menampilkan ${endIndex} dari ${total} karyawan`;

            // Load more button visibility
            if (endIndex < total) {
                loadMoreBtn.classList.remove('hidden');
            } else {
                loadMoreBtn.classList.add('hidden');
            }

            // Render pagination pills
            renderPaginationButtons(maxPage);
        }

        function renderPaginationButtons(maxPage) {
            pageNumberButtons.innerHTML = '';
            if (maxPage <= 1) return;

            // Prev Button
            const prevBtn = document.createElement('button');
            prevBtn.type = 'button';
            prevBtn.className = `px-2.5 py-1.5 rounded-lg text-xs font-semibold border ${currentPage === 1 ? 'border-slate-200 text-slate-300 pointer-events-none' : 'border-slate-200 text-slate-600 hover:bg-slate-100'}`;
            prevBtn.textContent = '←';
            prevBtn.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderCards();
                }
            };
            pageNumberButtons.appendChild(prevBtn);

            // Page numbers (limit 5)
            let start = Math.max(1, currentPage - 2);
            let end = Math.min(maxPage, start + 4);
            if (end - start < 4) {
                start = Math.max(1, end - 4);
            }

            for (let p = start; p <= end; p++) {
                const pBtn = document.createElement('button');
                pBtn.type = 'button';
                pBtn.className = `px-3 py-1.5 rounded-lg text-xs font-semibold border ${p === currentPage ? 'bg-indigo-600 border-indigo-600 text-white' : 'border-slate-200 text-slate-600 hover:bg-slate-100'}`;
                pBtn.textContent = p;
                pBtn.onclick = () => {
                    currentPage = p;
                    renderCards();
                    window.scrollTo({ top: document.getElementById('employeeGrid').offsetTop - 120, behavior: 'smooth' });
                };
                pageNumberButtons.appendChild(pBtn);
            }

            // Next Button
            const nextBtn = document.createElement('button');
            nextBtn.type = 'button';
            nextBtn.className = `px-2.5 py-1.5 rounded-lg text-xs font-semibold border ${currentPage === maxPage ? 'border-slate-200 text-slate-300 pointer-events-none' : 'border-slate-200 text-slate-600 hover:bg-slate-100'}`;
            nextBtn.textContent = '→';
            nextBtn.onclick = () => {
                if (currentPage < maxPage) {
                    currentPage++;
                    renderCards();
                }
            };
            pageNumberButtons.appendChild(nextBtn);
        }

        // Lazy load button click
        loadMoreBtn.addEventListener('click', function() {
            currentPage++;
            renderCards();
        });

        // Search input events
        searchInput.addEventListener('input', applyFilter);
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            applyFilter();
            searchInput.focus();
        });

        // Per page select event
        itemsPerPageSelect.addEventListener('change', function() {
            perPage = parseInt(this.value, 10);
            currentPage = 1;
            renderCards();
        });

        // Department button click events
        deptButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                deptButtons.forEach(b => {
                    b.classList.remove('bg-indigo-600', 'text-white');
                    b.classList.add('bg-slate-100', 'text-slate-600');
                });
                this.classList.remove('bg-slate-100', 'text-slate-600');
                this.classList.add('bg-indigo-600', 'text-white');

                currentDept = this.getAttribute('data-dept');
                applyFilter();
            });
        });

        // Open modal on card click
        allCards.forEach(card => {
            card.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const code = this.getAttribute('data-code');
                const dept = this.getAttribute('data-dept');
                const status = this.getAttribute('data-status');
                const inTime = this.getAttribute('data-in');
                const outTime = this.getAttribute('data-out');

                modalInitials.textContent = name.substring(0, 2).toUpperCase();
                modalEmployeeName.textContent = name;
                modalEmployeeCode.textContent = `ID: ${code}`;
                modalEmployeeDept.textContent = dept;

                employeeIdInputs.forEach(input => input.value = id);

                // Set status text and button states
                if (status === 'completed') {
                    modalStatusText.textContent = `Selesai (Masuk ${inTime}, Pulang ${outTime})`;
                    modalStatusText.className = 'font-semibold text-blue-600';
                    modalBtnMasuk.disabled = true;
                    modalBtnPulang.disabled = false; // allow re-checkout update
                } else if (status === 'clocked-in') {
                    modalStatusText.textContent = `Sudah Masuk Pukul ${inTime}`;
                    modalStatusText.className = 'font-semibold text-emerald-600';
                    modalBtnMasuk.disabled = true;
                    modalBtnPulang.disabled = false;
                } else {
                    modalStatusText.textContent = 'Belum Absen Masuk Hari Ini';
                    modalStatusText.className = 'font-semibold text-slate-500';
                    modalBtnMasuk.disabled = false;
                    modalBtnPulang.disabled = true;
                }

                actionModal.classList.remove('hidden');
            });
        });

        // Close modal
        closeModalBtn.addEventListener('click', () => actionModal.classList.add('hidden'));
        actionModal.addEventListener('click', (e) => {
            if (e.target === actionModal) actionModal.classList.add('hidden');
        });

        // Escape key to close modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !actionModal.classList.contains('hidden')) {
                actionModal.classList.add('hidden');
            }
        });

        // Initial render
        applyFilter();
    });
</script>
@endsection
