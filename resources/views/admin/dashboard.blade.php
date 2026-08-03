@extends('layouts.app')

@section('title', 'Dashboard Admin - Indorapet')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header Summary & Navigation -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-[#FFF9E6] p-6 rounded-3xl border-3 border-black shadow-[6px_6px_0px_0px_#000]">
        <div>
            <span class="inline-block rounded-xl bg-[#00F0FF] px-3 py-1 text-[11px] font-black text-black border-2 border-black shadow-[2px_2px_0px_0px_#000] uppercase tracking-wider mb-2">
                📊 CONTROL PANEL
            </span>
            <h2 class="text-2xl font-black text-black uppercase tracking-tight">Rekap Absensi Karyawan</h2>
            <p class="text-xs font-bold text-black uppercase tracking-wide">Kelola kehadiran harian, perizinan, dan laporan ekspor.</p>
        </div>
        
        <div class="flex flex-wrap gap-2.5">
            <!-- Settings Jam Masuk Trigger -->
            <button type="button" onclick="openSettingsModal()"
                class="inline-flex items-center gap-2 rounded-xl bg-[#FFE600] px-3.5 py-2 text-xs font-black text-black border-2 border-black shadow-[3px_3px_0px_0px_#000] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0px_0px_#000] transition-all"
                title="Atur jam masuk standar absensi">
                ⚡ Jam Masuk: {{ $jamMasukStandar }}
            </button>

            <button type="button" onclick="openManualModal()" 
                class="inline-flex items-center gap-2 rounded-xl bg-[#00F0FF] px-3.5 py-2 text-xs font-black text-black border-2 border-black shadow-[3px_3px_0px_0px_#000] hover:bg-[#FFE600] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0px_0px_#000] transition-all">
                ✏️ Input Manual
            </button>

            <!-- Export Mingguan Trigger -->
            <button type="button" onclick="openWeeklyExportModal()"
                class="inline-flex items-center gap-2 rounded-xl bg-[#54EA54] px-3.5 py-2 text-xs font-black text-black border-2 border-black shadow-[3px_3px_0px_0px_#000] hover:bg-[#FFE600] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0px_0px_#000] transition-all">
                📥 Export Mingguan
            </button>

            <!-- Export Rincian Trigger -->
            <button type="button" onclick="openRincianExportModal()"
                class="inline-flex items-center gap-2 rounded-xl bg-[#FF66C4] px-3.5 py-2 text-xs font-black text-black border-2 border-black shadow-[3px_3px_0px_0px_#000] hover:bg-[#FFE600] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0px_0px_#000] transition-all">
                📄 Export Rincian Harian
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-3xl p-6 border-3 border-black shadow-[6px_6px_0px_0px_#000]">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 m-0">
            <div>
                <label class="block text-xs font-black text-black uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input type="date" name="dari" value="{{ $dari }}" 
                    class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
            </div>
            
            <div>
                <label class="block text-xs font-black text-black uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ $sampai }}" 
                    class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
            </div>

            <div>
                <label class="block text-xs font-black text-black uppercase tracking-wider mb-1">Departemen</label>
                <select name="departemen" 
                    class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
                    <option value="ALL" {{ $departemenFilter === 'ALL' ? 'selected' : '' }}>Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ $departemenFilter === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" 
                    class="w-full rounded-xl bg-black py-2.5 px-4 text-xs font-black text-white uppercase tracking-wider border-2 border-black shadow-[3px_3px_0px_0px_#000] hover:bg-[#FFE600] hover:text-black hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0px_0px_#000] transition-all">
                    🔍 Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white rounded-3xl border-3 border-black shadow-[8px_8px_0px_0px_#000] overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-black text-white border-b-3 border-black">
                        <th class="p-4 text-xs font-black uppercase tracking-wider">Tanggal</th>
                        <th class="p-4 text-xs font-black uppercase tracking-wider">Nama Karyawan</th>
                        <th class="p-4 text-xs font-black uppercase tracking-wider">Departemen</th>
                        <th class="p-4 text-xs font-black uppercase tracking-wider">Jam Masuk</th>
                        <th class="p-4 text-xs font-black uppercase tracking-wider">Jam Keluar</th>
                        <th class="p-4 text-xs font-black uppercase tracking-wider">Durasi Kerja</th>
                        <th class="p-4 text-xs font-black uppercase tracking-wider">Keterangan</th>
                        <th class="p-4 text-xs font-black uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-black/10 text-sm">
                    @forelse($attendances as $att)
                        @php
                            $duration = '-';
                            if ($att->jam_masuk && $att->jam_keluar) {
                                $in = \Carbon\Carbon::parse($att->jam_masuk);
                                $out = \Carbon\Carbon::parse($att->jam_keluar);
                                $diff = $in->diff($out);
                                $duration = sprintf('%02d:%02d', $diff->h, $diff->i);
                            }

                            // Calculate Tardiness (Only if jam_masuk is strictly AFTER jam_masuk_standar)
                            $isLate = false;
                            $lateFormatted = '';
                            if ($att->jam_masuk && !$att->keterangan) {
                                $inTime = \Carbon\Carbon::parse('2000-01-01 ' . \Carbon\Carbon::parse($att->jam_masuk)->format('H:i:s'));
                                $standardTime = \Carbon\Carbon::parse('2000-01-01 ' . \Carbon\Carbon::parse($jamMasukStandar)->format('H:i:s'));
                                if ($inTime->greaterThan($standardTime)) {
                                    $isLate = true;
                                    $diffMin = abs((int) $inTime->diffInMinutes($standardTime));
                                    $hours = floor($diffMin / 60);
                                    $mins = $diffMin % 60;
                                    if ($hours > 0) {
                                        $lateFormatted = $mins > 0 ? "{$hours}j {$mins}m" : "{$hours}j";
                                    } else {
                                        $lateFormatted = "{$mins}m";
                                    }
                                }
                            }
                        @endphp
                        <tr class="hover:bg-[#FFF9E6] transition-colors">
                            <td class="p-4 font-black text-black text-xs">
                                {{ Carbon\Carbon::parse($att->tanggal)->format('d M Y') }}
                            </td>
                            <td class="p-4">
                                <div class="font-black text-black">{{ $att->employee->nama }}</div>
                                <div class="inline-block bg-[#FFE600] text-black font-mono font-bold text-[10px] px-1.5 py-0.5 rounded border border-black shadow-[1px_1px_0px_0px_#000] mt-0.5">ID: {{ $att->employee->kode_karyawan }}</div>
                            </td>
                            <td class="p-4">
                                <span class="inline-block bg-white text-black border border-black font-extrabold text-xs px-2 py-0.5 rounded-md shadow-[1.5px_1.5px_0px_0px_#000]">
                                    {{ $att->employee->departemen }}
                                </span>
                            </td>
                            <td class="p-4">
                                @if($att->jam_masuk)
                                    <div class="font-mono font-bold text-xs text-black">{{ Carbon\Carbon::parse($att->jam_masuk)->format('H:i') }}</div>
                                    @if($isLate)
                                        <span class="inline-flex items-center rounded-md bg-[#FF4747] text-white border border-black px-1.5 py-0.5 text-[10px] font-black shadow-[1.5px_1.5px_0px_0px_#000] mt-0.5">
                                            Telat {{ $lateFormatted }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-400 font-bold">-</span>
                                @endif
                            </td>
                            <td class="p-4 font-mono font-bold text-xs text-black">
                                {{ $att->jam_keluar ? Carbon\Carbon::parse($att->jam_keluar)->format('H:i') : '-' }}
                            </td>
                            <td class="p-4 font-mono font-bold text-xs text-black">
                                {{ $duration }}
                            </td>
                            <td class="p-4">
                                @if($att->keterangan)
                                    <span class="inline-block bg-[#FFE600] text-black border border-black px-2.5 py-0.5 text-xs font-black rounded-md shadow-[1.5px_1.5px_0px_0px_#000]">
                                        {{ $att->keterangan }}
                                    </span>
                                @else
                                    <span class="text-slate-400 font-bold">-</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <!-- Edit Button -->
                                    <button type="button" 
                                        onclick="openEditModal({{ json_encode([
                                            'employee_id' => $att->employee_id,
                                            'nama' => $att->employee->nama,
                                            'tanggal' => $att->tanggal->toDateString(),
                                            'jam_masuk' => $att->jam_masuk ? Carbon\Carbon::parse($att->jam_masuk)->format('H:i') : '',
                                            'jam_keluar' => $att->jam_keluar ? Carbon\Carbon::parse($att->jam_keluar)->format('H:i') : '',
                                            'keterangan' => $att->keterangan ?? ''
                                        ]) }})"
                                        class="rounded-xl bg-[#FFE600] p-2 text-black border border-black font-black shadow-[2px_2px_0px_0px_#000] hover:bg-[#00F0FF] transition-all"
                                        title="Edit Kehadiran">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>

                                    <!-- Quick Export Rincian -->
                                    <a href="{{ route('admin.export.rincian', ['employee' => $att->employee_id]) }}?dari={{ $dari }}&sampai={{ $sampai }}"
                                        class="rounded-xl bg-[#00F0FF] p-2 text-black border border-black font-black shadow-[2px_2px_0px_0px_#000] hover:bg-[#FFE600] transition-all"
                                        title="Export Excel Harian">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-black font-black uppercase tracking-wider">
                                Tidak ada data absensi untuk rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Manual / Edit Attendance -->
<div id="manualModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in" aria-modal="true" role="dialog">
    <div class="w-full max-w-md transform overflow-hidden rounded-3xl bg-[#FFFDF5] p-6 sm:p-8 shadow-[12px_12px_0px_0px_#000] border-4 border-black flex flex-col space-y-4">
        
        <div class="flex items-center justify-between border-b-3 border-black pb-3">
            <h3 class="text-base font-black text-black uppercase tracking-tight" id="modalTitle">Input Absensi Manual</h3>
            <button type="button" onclick="closeManualModal()" class="p-1 rounded-xl bg-white border-2 border-black text-black hover:bg-[#FF4747] hover:text-white shadow-[2px_2px_0px_0px_#000] transition-colors">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.attendance.update') }}" class="space-y-4 m-0">
            @csrf
            
            <input type="hidden" name="employee_id" id="modalEmployeeIdReal">

            <!-- Employee Selection -->
            <div id="employeeSelectWrapper">
                <label class="block text-xs font-black text-black uppercase tracking-wider mb-1">Pilih Karyawan</label>
                <select id="modalEmployeeSelect" required
                    onchange="document.getElementById('modalEmployeeIdReal').value = this.value"
                    class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->nama }} ({{ $emp->departemen }})</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Employee Text Display -->
            <div id="employeeTextWrapper" class="hidden">
                <label class="block text-xs font-black text-black uppercase tracking-wider mb-1">Karyawan</label>
                <input type="text" id="modalEmployeeText" readonly
                    class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-[#FFE600] cursor-not-allowed">
            </div>

            <!-- Date -->
            <div>
                <label class="block text-xs font-black text-black uppercase tracking-wider mb-1">Tanggal</label>
                <input type="date" name="tanggal" id="modalTanggal" required
                    class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
            </div>

            <!-- Times -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-black text-black uppercase tracking-wider mb-1">Jam Masuk</label>
                    <input type="time" name="jam_masuk" id="modalJamMasuk"
                        class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
                </div>
                <div>
                    <label class="block text-xs font-black text-black uppercase tracking-wider mb-1">Jam Keluar</label>
                    <input type="time" name="jam_keluar" id="modalJamKeluar"
                        class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label class="block text-xs font-black text-black uppercase tracking-wider mb-1">Keterangan (Izin/Alpha/Sakit)</label>
                <select name="keterangan" id="modalKeterangan"
                    class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
                    <option value="">-- Tanpa Keterangan (Hadir) --</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Alpha">Alpha</option>
                    <option value="Cuti">Cuti</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="pt-2">
                <button type="submit" 
                    class="w-full flex justify-center rounded-2xl bg-[#00F0FF] py-3 px-4 text-xs font-black text-black uppercase tracking-wider border-3 border-black shadow-[4px_4px_0px_0px_#000] hover:bg-[#FFE600] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_#000] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[2px_2px_0px_0px_#000] transition-all">
                    💾 Simpan Kehadiran
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Weekly Export -->
<div id="weeklyExportModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in" aria-modal="true" role="dialog">
    <div class="w-full max-w-sm transform overflow-hidden rounded-3xl bg-[#FFFDF5] p-6 shadow-[12px_12px_0px_0px_#000] border-4 border-black flex flex-col space-y-4">
        <div class="flex items-center justify-between border-b-3 border-black pb-3">
            <h3 class="text-base font-black text-black uppercase tracking-tight">Export Catatan Mingguan</h3>
            <button type="button" onclick="closeWeeklyExportModal()" class="p-1 rounded-xl bg-white border-2 border-black text-black hover:bg-[#FF4747] hover:text-white shadow-[2px_2px_0px_0px_#000] transition-colors">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>
        <p class="text-xs font-bold text-black uppercase">Pilih rentang tanggal (maksimum 7 hari) untuk export format mingguan.</p>

        <form method="GET" action="{{ route('admin.export.mingguan') }}" class="space-y-4 m-0" onsubmit="return validateWeeklyRange()">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-black text-black uppercase tracking-wider mb-1">Mulai</label>
                    <input type="date" name="dari" id="weeklyExportDari" required value="{{ $dari }}"
                        class="block w-full rounded-xl border-2 border-black py-2 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
                </div>
                <div>
                    <label class="block text-xs font-black text-black uppercase tracking-wider mb-1">Selesai</label>
                    <input type="date" name="sampai" id="weeklyExportSampai" required value="{{ $sampai }}"
                        class="block w-full rounded-xl border-2 border-black py-2 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
                </div>
            </div>
            <div>
                <button type="submit" 
                    class="w-full flex justify-center rounded-2xl bg-[#54EA54] py-3 px-4 text-xs font-black text-black uppercase tracking-wider border-3 border-black shadow-[4px_4px_0px_0px_#000] hover:bg-[#FFE600] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_#000] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[2px_2px_0px_0px_#000] transition-all">
                    📥 Download Excel Mingguan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Rincian Export -->
<div id="rincianExportModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in" aria-modal="true" role="dialog">
    <div class="w-full max-w-sm transform overflow-hidden rounded-3xl bg-[#FFFDF5] p-6 shadow-[12px_12px_0px_0px_#000] border-4 border-black flex flex-col space-y-4">
        <div class="flex items-center justify-between border-b-3 border-black pb-3">
            <h3 class="text-base font-black text-black uppercase tracking-tight">Export Rincian Harian</h3>
            <button type="button" onclick="closeRincianExportModal()" class="p-1 rounded-xl bg-white border-2 border-black text-black hover:bg-[#FF4747] hover:text-white shadow-[2px_2px_0px_0px_#000] transition-colors">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>
        <p class="text-xs font-bold text-black uppercase">Pilih karyawan dan rentang tanggal untuk export rincian harian.</p>

        <form onsubmit="submitRincianExport(event)" class="space-y-4 m-0">
            <div>
                <label class="block text-xs font-black text-black uppercase tracking-wider mb-1">Pilih Karyawan</label>
                <select id="rincianExportEmployee" required
                    class="block w-full rounded-xl border-2 border-black py-2 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->nama }} ({{ $emp->departemen }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-black text-black uppercase tracking-wider mb-1">Mulai</label>
                    <input type="date" id="rincianExportDari" required value="{{ $dari }}"
                        class="block w-full rounded-xl border-2 border-black py-2 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
                </div>
                <div>
                    <label class="block text-xs font-black text-black uppercase tracking-wider mb-1">Selesai</label>
                    <input type="date" id="rincianExportSampai" required value="{{ $sampai }}"
                        class="block w-full rounded-xl border-2 border-black py-2 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
                </div>
            </div>
            <div>
                <button type="submit" 
                    class="w-full flex justify-center rounded-2xl bg-[#FF66C4] py-3 px-4 text-xs font-black text-black uppercase tracking-wider border-3 border-black shadow-[4px_4px_0px_0px_#000] hover:bg-[#FFE600] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_#000] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[2px_2px_0px_0px_#000] transition-all">
                    📄 Download Excel Rincian
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Settings Jam Kerja -->
<div id="settingsModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in" aria-modal="true" role="dialog">
    <div class="w-full max-w-sm transform overflow-hidden rounded-3xl bg-[#FFFDF5] p-6 shadow-[12px_12px_0px_0px_#000] border-4 border-black flex flex-col space-y-4">
        <div class="flex items-center justify-between border-b-3 border-black pb-3">
            <h3 class="text-base font-black text-black uppercase tracking-tight">Pengaturan Jam Absensi</h3>
            <button type="button" onclick="closeSettingsModal()" class="p-1 rounded-xl bg-white border-2 border-black text-black hover:bg-[#FF4747] hover:text-white shadow-[2px_2px_0px_0px_#000] transition-colors">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>
        <p class="text-xs font-bold text-black uppercase">Tentukan jam masuk standar. Karyawan yang absen masuk setelah jam ini akan dianggap telat.</p>

        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4 m-0">
            @csrf
            <div>
                <label class="block text-xs font-black text-black uppercase tracking-wider mb-1">Jam Masuk Standar</label>
                <input type="time" name="jam_masuk_standar" id="settingJamMasuk" required value="{{ $jamMasukStandar }}"
                    class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
            </div>
            <div>
                <button type="submit" 
                    class="w-full flex justify-center rounded-2xl bg-[#FFE600] py-3 px-4 text-xs font-black text-black uppercase tracking-wider border-3 border-black shadow-[4px_4px_0px_0px_#000] hover:bg-[#00F0FF] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_#000] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[2px_2px_0px_0px_#000] transition-all">
                    ⚙️ Simpan Pengaturan
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

    // Open Modal for Manual Input (Resetting fields)
    function openManualModal() {
        document.getElementById('modalTitle').textContent = 'Input Absensi Manual';
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
        manualModal.classList.add('flex');
    }

    // Open Modal for Edit
    function openEditModal(data) {
        document.getElementById('modalTitle').textContent = 'Edit Data Absensi';
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
        manualModal.classList.add('flex');
    }

    function closeManualModal() {
        manualModal.classList.add('hidden');
        manualModal.classList.remove('flex');
    }

    // Weekly Export
    function openWeeklyExportModal() {
        weeklyExportModal.classList.remove('hidden');
        weeklyExportModal.classList.add('flex');
    }
    function closeWeeklyExportModal() {
        weeklyExportModal.classList.add('hidden');
        weeklyExportModal.classList.remove('flex');
    }
    function validateWeeklyRange() {
        const dariStr = document.getElementById('weeklyExportDari').value;
        const sampaiStr = document.getElementById('weeklyExportSampai').value;
        if (!dariStr || !sampaiStr) return false;

        const dari = new Date(dariStr);
        const sampai = new Date(sampaiStr);
        const diffTime = Math.abs(sampai - dari);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // inclusive

        if (diffDays > 7) {
            alert('Format mingguan membatasi ekspor maksimal 7 hari. Silakan pilih rentang tanggal yang lebih pendek.');
            return false;
        }
        closeWeeklyExportModal();
        return true;
    }

    // Rincian Export
    function openRincianExportModal() {
        rincianExportModal.classList.remove('hidden');
        rincianExportModal.classList.add('flex');
    }
    function closeRincianExportModal() {
        rincianExportModal.classList.add('hidden');
        rincianExportModal.classList.remove('flex');
    }
    function submitRincianExport(event) {
        event.preventDefault();
        const employeeId = document.getElementById('rincianExportEmployee').value;
        const dari = document.getElementById('rincianExportDari').value;
        const sampai = document.getElementById('rincianExportSampai').value;

        if (!employeeId) {
            alert('Pilih karyawan terlebih dahulu.');
            return;
        }

        const url = `/admin/export/rincian/${employeeId}?dari=${dari}&sampai=${sampai}`;
        closeRincianExportModal();
        window.location.href = url;
    }

    // Handle clicks outside modal to close
    window.addEventListener('click', function(e) {
        if (e.target === manualModal) closeManualModal();
        if (e.target === weeklyExportModal) closeWeeklyExportModal();
        if (e.target === rincianExportModal) closeRincianExportModal();
        if (e.target === settingsModal) closeSettingsModal();
    });

    // Settings Modal
    const settingsModal = document.getElementById('settingsModal');
    function openSettingsModal() {
        settingsModal.classList.remove('hidden');
        settingsModal.classList.add('flex');
    }
    function closeSettingsModal() {
        settingsModal.classList.add('hidden');
        settingsModal.classList.remove('flex');
    }
</script>
@endsection
