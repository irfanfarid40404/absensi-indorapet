@extends('layouts.app')

@section('title', 'Dashboard Presensi - PT Indorapet')

@section('content')
<div class="space-y-6">

    <!-- Header Action Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700">
                    Administrator
                </span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 mt-1">Rekapitulasi Presensi Karyawan</h1>
            <p class="text-xs text-slate-500">Kelola catatan jam masuk, perizinan, pengaturan jadwal kerja, dan cetak laporan Excel.</p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="openSettingsModal()"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 transition-all">
                <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Standar: {{ $jamMasukStandar }}</span>
            </button>

            <button type="button" onclick="openManualModal()" 
                    class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-indigo-500 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>Input Manual</span>
            </button>

            <button type="button" onclick="openWeeklyExportModal()"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 transition-all">
                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                <span>Export Mingguan</span>
            </button>

            <button type="button" onclick="openRincianExportModal()"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 transition-all">
                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                <span>Export Rincian</span>
            </button>
        </div>
    </div>

    <!-- Executive KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-500">Total Hadir (Periode Ini)</span>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalRecords, 0, ',', '.') }}</p>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Dari filter tanggal aktif</span>
            </div>
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-500">Tepat Waktu (≤ {{ $jamMasukStandar }})</span>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($tepatWaktuCount, 0, ',', '.') }}</p>
                <span class="text-[11px] text-slate-400 mt-0.5 block">
                    {{ $totalRecords > 0 ? round(($tepatWaktuCount / $totalRecords) * 100, 1) : 0 }}% dari kehadiran
                </span>
            </div>
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-500">Terlambat (> {{ $jamMasukStandar }})</span>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($terlambatCount, 0, ',', '.') }}</p>
                <span class="text-[11px] text-slate-400 mt-0.5 block">
                    {{ $totalRecords > 0 ? round(($terlambatCount / $totalRecords) * 100, 1) : 0 }}% dari kehadiran
                </span>
            </div>
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-500">Total Karyawan Aktif</span>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $totalKaryawanAktif }}</p>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Semua cabang toko</span>
            </div>
            <div class="p-3 bg-slate-100 text-slate-600 rounded-xl">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
        </div>

    </div>

    <!-- Filters & Search Form -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 m-0">
            
            <!-- Search by Name or ID -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Cari Karyawan</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nama atau ID Karyawan..." 
                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all">
                </div>
            </div>

            <!-- Dari Tanggal -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" name="dari" value="{{ $dari }}" 
                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all">
            </div>
            
            <!-- Sampai Tanggal -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ $sampai }}" 
                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all">
            </div>

            <!-- Departemen -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Departemen</label>
                <select name="departemen" 
                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all">
                    <option value="ALL" {{ $departemenFilter === 'ALL' ? 'selected' : '' }}>Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ $departemenFilter === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Actions & Per-page -->
            <div class="flex items-end gap-2">
                <button type="submit" 
                        class="flex-1 rounded-xl bg-indigo-600 py-2 px-3 text-xs font-semibold text-white shadow-xs hover:bg-indigo-500 transition-all flex items-center justify-center gap-1.5">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                    </svg>
                    <span>Filter</span>
                </button>
                @if(request()->hasAny(['search', 'departemen', 'dari', 'sampai']) && ($search != '' || $departemenFilter != 'ALL'))
                    <a href="{{ route('admin.dashboard') }}" 
                       class="rounded-xl border border-slate-200 bg-white py-2 px-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-all" title="Reset filter">
                        ✕
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Attendance Table with Server-side Pagination -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-semibold text-slate-900">Daftar Kehadiran</h3>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                    {{ $attendances->total() }} data
                </span>
            </div>
            
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <span>Baris per halaman:</span>
                <select onchange="window.location.href = this.value" class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-xs text-slate-700">
                    @foreach([10, 15, 25, 50, 100] as $size)
                        <option value="{{ request()->fullUrlWithQuery(['per_page' => $size]) }}" {{ $perPage == $size ? 'selected' : '' }}>
                            {{ $size }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 border-b border-slate-200 font-semibold">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Karyawan</th>
                        <th class="py-3 px-4">Departemen</th>
                        <th class="py-3 px-4">Jam Masuk</th>
                        <th class="py-3 px-4">Jam Keluar</th>
                        <th class="py-3 px-4">Durasi Kerja</th>
                        <th class="py-3 px-4">Keterangan</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($attendances as $att)
                        @php
                            $duration = '-';
                            if ($att->jam_masuk && $att->jam_keluar) {
                                $in = \Carbon\Carbon::parse($att->jam_masuk);
                                $out = \Carbon\Carbon::parse($att->jam_keluar);
                                $diff = $in->diff($out);
                                $duration = "{$diff->h}j {$diff->i}m";
                            }
                            
                            $isLate = false;
                            if ($att->jam_masuk) {
                                $isLate = \Carbon\Carbon::parse($att->jam_masuk)->format('H:i') > \Carbon\Carbon::parse($jamMasukStandar)->format('H:i');
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <!-- Tanggal -->
                            <td class="py-3.5 px-4 font-medium text-slate-900 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($att->tanggal)->format('d M Y') }}
                            </td>
                            
                            <!-- Karyawan -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700 font-bold text-[10px] uppercase">
                                        {{ substr($att->employee->nama ?? '?', 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $att->employee->nama ?? '-' }}</div>
                                        <div class="font-mono text-[10px] text-slate-400">ID: {{ $att->employee->kode_karyawan ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Departemen -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-600">
                                    {{ $att->employee->departemen ?? '-' }}
                                </span>
                            </td>
                            
                            <!-- Jam Masuk & Status -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($att->jam_masuk)
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono font-medium text-slate-800">{{ \Carbon\Carbon::parse($att->jam_masuk)->format('H:i') }}</span>
                                        @if($isLate)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                                                Telat
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                                Tepat
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            
                            <!-- Jam Keluar -->
                            <td class="py-3.5 px-4 font-mono text-slate-800 whitespace-nowrap">
                                {{ $att->jam_keluar ? \Carbon\Carbon::parse($att->jam_keluar)->format('H:i') : '-' }}
                            </td>
                            
                            <!-- Durasi -->
                            <td class="py-3.5 px-4 font-mono text-slate-600 whitespace-nowrap">
                                {{ $duration }}
                            </td>
                            
                            <!-- Keterangan -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($att->keterangan)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-700">
                                        {{ $att->keterangan }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            
                            <!-- Aksi Edit -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <button type="button" 
                                        onclick="openEditModal({
                                            employee_id: '{{ $att->employee_id }}',
                                            nama: '{{ addslashes($att->employee->nama ?? '') }}',
                                            tanggal: '{{ is_object($att->tanggal) ? $att->tanggal->format('Y-m-d') : substr($att->tanggal, 0, 10) }}',
                                            jam_masuk: '{{ $att->jam_masuk ? \Carbon\Carbon::parse($att->jam_masuk)->format('H:i') : '' }}',
                                            jam_keluar: '{{ $att->jam_keluar ? \Carbon\Carbon::parse($att->jam_keluar)->format('H:i') : '' }}',
                                            keterangan: '{{ addslashes($att->keterangan ?? '') }}'
                                        })"
                                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-[11px] font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 transition-colors">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                    <span>Edit</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <svg class="mx-auto h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                                <p class="mt-2 text-xs font-semibold text-slate-600">Tidak ada catatan presensi pada periode/filter ini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($attendances->hasPages() || $attendances->total() > 0)
            <div class="px-4 py-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <div>
                    Menampilkan <span class="font-semibold text-slate-800">{{ $attendances->firstItem() ?? 0 }}</span> sampai 
                    <span class="font-semibold text-slate-800">{{ $attendances->lastItem() ?? 0 }}</span> dari 
                    <span class="font-semibold text-slate-800">{{ $attendances->total() }}</span> total presensi
                </div>
                
                <div>
                    {{ $attendances->links('pagination::tailwind') }}
                </div>
            </div>
        @endif

    </div>

</div>

<!-- Modal Manual Input / Edit Absensi -->
<div id="manualModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity" aria-modal="true" role="dialog">
    <div class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl border border-slate-200 transition-all flex flex-col space-y-4">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900" id="modalTitle">Input Presensi Manual</h3>
            <button type="button" onclick="closeManualModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.attendance.update') }}" class="space-y-4 m-0">
            @csrf
            
            <input type="hidden" name="employee_id" id="modalEmployeeIdReal">
            
            <!-- Employee Select (for New entry) -->
            <div id="employeeSelectWrapper">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Karyawan</label>
                <select id="modalEmployeeSelect" onchange="document.getElementById('modalEmployeeIdReal').value = this.value"
                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->nama }} ({{ $emp->kode_karyawan }} - {{ $emp->departemen }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Employee Readonly Text (for Edit entry) -->
            <div id="employeeTextWrapper" class="hidden">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Karyawan</label>
                <input type="text" id="modalEmployeeText" readonly 
                       class="block w-full rounded-xl border border-slate-200 bg-slate-100 py-2 px-3 text-xs font-semibold text-slate-700 cursor-not-allowed">
            </div>

            <!-- Tanggal -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal</label>
                <input type="date" name="tanggal" id="modalTanggal" required
                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
            </div>

            <!-- Jam Masuk & Keluar -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Masuk</label>
                    <input type="time" name="jam_masuk" id="modalJamMasuk"
                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Keluar</label>
                    <input type="time" name="jam_keluar" id="modalJamKeluar"
                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Khusus</label>
                <select name="keterangan" id="modalKeterangan"
                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
                    <option value="">-- Hadir Normal --</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Alpha">Alpha</option>
                    <option value="Cuti">Cuti</option>
                </select>
            </div>

            <div class="pt-2">
                <button type="submit" 
                        class="w-full rounded-xl bg-indigo-600 py-2.5 px-4 text-xs font-semibold text-white shadow-xs hover:bg-indigo-500 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Settings Jam Masuk Standar -->
<div id="settingsModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity" aria-modal="true" role="dialog">
    <div class="w-full max-w-sm transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl border border-slate-200 transition-all flex flex-col space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900">Pengaturan Jam Masuk</h3>
            <button type="button" onclick="closeSettingsModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>
        <p class="text-xs text-slate-500">Batas jam masuk toko. Presensi setelah jam ini akan otomatis ditandai status "Telat".</p>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4 m-0">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Masuk Standar</label>
                <input type="time" name="jam_masuk_standar" id="settingJamMasuk" required value="{{ $jamMasukStandar }}"
                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 px-3 text-sm text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
            </div>
            <div>
                <button type="submit" 
                        class="w-full rounded-xl bg-indigo-600 py-2.5 px-4 text-xs font-semibold text-white shadow-xs hover:bg-indigo-500 transition-all">
                    Simpan Jam Standar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Weekly Export -->
<div id="weeklyExportModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity" aria-modal="true" role="dialog">
    <div class="w-full max-w-sm transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl border border-slate-200 transition-all flex flex-col space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900">Export Laporan Mingguan</h3>
            <button type="button" onclick="closeWeeklyExportModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>
        <p class="text-xs text-slate-500">Pilih rentang tanggal (maksimal 7 hari) untuk mengunduh rekap matriks mingguan seluruh karyawan.</p>

        <form method="GET" action="{{ route('admin.export.mingguan') }}" class="space-y-4 m-0" onsubmit="return validateWeeklyRange()">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mulai</label>
                    <input type="date" name="dari" id="weeklyExportDari" required value="{{ $dari }}"
                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Selesai</label>
                    <input type="date" name="sampai" id="weeklyExportSampai" required value="{{ $sampai }}"
                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
                </div>
            </div>
            <div>
                <button type="submit" 
                        class="w-full rounded-xl bg-emerald-600 py-2.5 px-4 text-xs font-semibold text-white shadow-xs hover:bg-emerald-500 transition-all flex items-center justify-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Unduh Excel Mingguan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Rincian Export -->
<div id="rincianExportModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity" aria-modal="true" role="dialog">
    <div class="w-full max-w-sm transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl border border-slate-200 transition-all flex flex-col space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900">Export Rincian Individu</h3>
            <button type="button" onclick="closeRincianExportModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>
        <p class="text-xs text-slate-500">Pilih satu karyawan dan rentang tanggal untuk mengunduh rekap rincian kehadiran.</p>

        <form onsubmit="submitRincianExport(event)" class="space-y-4 m-0">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Karyawan</label>
                <select id="rincianExportEmployee" required
                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->nama }} ({{ $emp->departemen }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mulai</label>
                    <input type="date" id="rincianExportDari" required value="{{ $dari }}"
                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Selesai</label>
                    <input type="date" id="rincianExportSampai" required value="{{ $sampai }}"
                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
                </div>
            </div>
            <div>
                <button type="submit" 
                        class="w-full rounded-xl bg-blue-600 py-2.5 px-4 text-xs font-semibold text-white shadow-xs hover:bg-blue-500 transition-all flex items-center justify-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    <span>Unduh Excel Rincian</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const manualModal = document.getElementById('manualModal');
    const weeklyExportModal = document.getElementById('weeklyExportModal');
    const rincianExportModal = document.getElementById('rincianExportModal');
    const settingsModal = document.getElementById('settingsModal');

    function openManualModal() {
        document.getElementById('modalTitle').textContent = 'Input Presensi Manual';
        document.getElementById('employeeSelectWrapper').classList.remove('hidden');
        document.getElementById('employeeTextWrapper').classList.add('hidden');
        document.getElementById('modalEmployeeSelect').required = true;
        document.getElementById('modalEmployeeSelect').value = '';
        document.getElementById('modalEmployeeIdReal').value = '';
        
        document.getElementById('modalTanggal').value = '{{ date('Y-m-d') }}';
        document.getElementById('modalJamMasuk').value = '';
        document.getElementById('modalJamKeluar').value = '';
        document.getElementById('modalKeterangan').value = '';

        manualModal.classList.remove('hidden');
    }

    function openEditModal(data) {
        document.getElementById('modalTitle').textContent = 'Edit Data Presensi';
        document.getElementById('employeeSelectWrapper').classList.add('hidden');
        document.getElementById('employeeTextWrapper').classList.remove('hidden');
        document.getElementById('modalEmployeeSelect').required = false;
        
        document.getElementById('modalEmployeeText').value = data.nama;
        document.getElementById('modalEmployeeIdReal').value = data.employee_id;
        document.getElementById('modalTanggal').value = data.tanggal;
        document.getElementById('modalJamMasuk').value = data.jam_masuk;
        document.getElementById('modalJamKeluar').value = data.jam_keluar;
        document.getElementById('modalKeterangan').value = data.keterangan;

        manualModal.classList.remove('hidden');
    }

    function closeManualModal() {
        manualModal.classList.add('hidden');
    }

    function openWeeklyExportModal() {
        weeklyExportModal.classList.remove('hidden');
    }
    function closeWeeklyExportModal() {
        weeklyExportModal.classList.add('hidden');
    }
    function validateWeeklyRange() {
        const dariStr = document.getElementById('weeklyExportDari').value;
        const sampaiStr = document.getElementById('weeklyExportSampai').value;
        if (!dariStr || !sampaiStr) return false;

        const dari = new Date(dariStr);
        const sampai = new Date(sampaiStr);
        const diffTime = Math.abs(sampai - dari);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

        if (diffDays > 7) {
            alert('Format mingguan membatasi ekspor maksimal 7 hari. Silakan pilih rentang tanggal maksimal 7 hari.');
            return false;
        }
        closeWeeklyExportModal();
        return true;
    }

    function openRincianExportModal() {
        rincianExportModal.classList.remove('hidden');
    }
    function closeRincianExportModal() {
        rincianExportModal.classList.add('hidden');
    }
    function submitRincianExport(event) {
        event.preventDefault();
        const employeeId = document.getElementById('rincianExportEmployee').value;
        const dari = document.getElementById('rincianExportDari').value;
        const sampai = document.getElementById('rincianExportSampai').value;

        if (!employeeId) {
            alert('Silakan pilih karyawan terlebih dahulu.');
            return;
        }

        const url = `/admin/export/rincian/${employeeId}?dari=${dari}&sampai=${sampai}`;
        closeRincianExportModal();
        window.location.href = url;
    }

    function openSettingsModal() {
        settingsModal.classList.remove('hidden');
    }
    function closeSettingsModal() {
        settingsModal.classList.add('hidden');
    }

    // Close on outside click
    window.addEventListener('click', function(e) {
        if (e.target === manualModal) closeManualModal();
        if (e.target === weeklyExportModal) closeWeeklyExportModal();
        if (e.target === rincianExportModal) closeRincianExportModal();
        if (e.target === settingsModal) closeSettingsModal();
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeManualModal();
            closeWeeklyExportModal();
            closeRincianExportModal();
            closeSettingsModal();
        }
    });
</script>
@endsection
