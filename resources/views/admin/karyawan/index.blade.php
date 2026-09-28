@extends('layouts.app')

@section('title', 'Manajemen Karyawan - PT Indorapet')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700">
                    Master Data
                </span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">PT Indorapet</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 mt-1">Data & Karyawan Toko</h1>
            <p class="text-xs text-slate-500">Kelola informasi identitas karyawan, departemen cabang, dan status hak akses presensi.</p>
        </div>

        <!-- Quick Summary Badges -->
        <div class="flex items-center gap-2">
            <div class="px-3 py-2 bg-slate-50 rounded-xl border border-slate-200/70 text-center">
                <span class="text-[10px] text-slate-500 font-medium block">Total</span>
                <span class="text-base font-bold text-slate-900">{{ $totalEmployees }}</span>
            </div>
            <div class="px-3 py-2 bg-emerald-50 rounded-xl border border-emerald-200/70 text-center">
                <span class="text-[10px] text-emerald-600 font-medium block">Aktif</span>
                <span class="text-base font-bold text-emerald-700">{{ $totalActive }}</span>
            </div>
            <div class="px-3 py-2 bg-slate-100 rounded-xl border border-slate-200 text-center">
                <span class="text-[10px] text-slate-500 font-medium block">Nonaktif</span>
                <span class="text-base font-bold text-slate-600">{{ $totalInactive }}</span>
            </div>
        </div>
    </div>

    <!-- Main Content: Left Form + Right Table -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Add Employee Card -->
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                <div class="border-b border-slate-100 pb-3 mb-4">
                    <h2 class="text-sm font-bold text-slate-900">Tambah Karyawan Baru</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Daftarkan karyawan baru ke sistem presensi.</p>
                </div>

                <form method="POST" action="{{ route('admin.karyawan.store') }}" class="space-y-4 m-0">
                    @csrf
                    
                    <!-- Kode Karyawan -->
                    <div>
                        <label for="kode_karyawan" class="block text-xs font-semibold text-slate-700 mb-1">
                            Kode / ID Karyawan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="kode_karyawan" id="kode_karyawan" required value="{{ old('kode_karyawan') }}"
                               placeholder="Contoh: 101"
                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
                        @error('kode_karyawan')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama" id="nama" required value="{{ old('nama') }}"
                               placeholder="Contoh: Budi Santoso"
                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
                        @error('nama')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Departemen -->
                    <div>
                        <label for="departemen" class="block text-xs font-semibold text-slate-700 mb-1">
                            Cabang / Departemen <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="departemen" id="departemen" required value="{{ old('departemen') }}"
                               placeholder="Contoh: INDORAPET2"
                               class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
                        @error('departemen')
                            <p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full flex justify-center items-center gap-1.5 rounded-xl bg-indigo-600 py-2.5 px-4 text-xs font-semibold text-white shadow-xs hover:bg-indigo-500 transition-all">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span>Simpan Karyawan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Filter Bar & Employee Table -->
        <div class="lg:col-span-2 space-y-4">
            
            <!-- Filters Card -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
                <form method="GET" action="{{ route('admin.karyawan') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 m-0">
                    
                    <!-- Search Input -->
                    <div class="sm:col-span-2">
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode..." 
                                   class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pl-9 pr-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
                        </div>
                    </div>

                    <!-- Filter Departemen -->
                    <div>
                        <select name="departemen" class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
                            <option value="ALL" {{ $departemenFilter === 'ALL' ? 'selected' : '' }}>Semua Cabang</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ $departemenFilter === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status & Submit -->
                    <div class="flex items-center gap-2">
                        <select name="status" class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-2.5 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
                            <option value="ALL" {{ $statusFilter === 'ALL' ? 'selected' : '' }}>Semua Status</option>
                            <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        
                        <button type="submit" class="rounded-xl bg-slate-800 text-white px-3 py-2 text-xs font-semibold hover:bg-slate-700 transition-colors">
                            Cari
                        </button>

                        @if(request()->hasAny(['search', 'departemen', 'status']) && ($search != '' || $departemenFilter != 'ALL' || $statusFilter != 'ALL'))
                            <a href="{{ route('admin.karyawan') }}" class="rounded-xl border border-slate-200 bg-white p-2 text-xs text-slate-600 hover:bg-slate-100 transition-colors" title="Reset filter">
                                ✕
                            </a>
                        @endif
                    </div>

                </form>
            </div>

            <!-- Employee List Table with Server-side Pagination -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-semibold text-slate-900">Daftar Karyawan</h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                            {{ $employees->total() }} orang
                        </span>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span>Per halaman:</span>
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
                                <th class="py-3 px-4">User ID</th>
                                <th class="py-3 px-4">Nama Lengkap</th>
                                <th class="py-3 px-4">Departemen</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($employees as $emp)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <!-- User ID -->
                                    <td class="py-3.5 px-4 font-mono font-semibold text-slate-700">
                                        {{ $emp->kode_karyawan }}
                                    </td>

                                    <!-- Nama Lengkap -->
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700 font-bold text-[10px] uppercase">
                                                {{ substr($emp->nama, 0, 2) }}
                                            </div>
                                            <span class="font-semibold text-slate-900">{{ $emp->nama }}</span>
                                        </div>
                                    </td>

                                    <!-- Departemen -->
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-600">
                                            {{ $emp->departemen }}
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-3.5 px-4">
                                        @if($emp->aktif)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Aksi Edit -->
                                    <td class="py-3.5 px-4 text-right">
                                        <button type="button" 
                                                onclick="openEditEmployeeModal({{ json_encode($emp) }})"
                                                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-[11px] font-medium text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 transition-colors"
                                                title="Edit Data Karyawan">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                            <span>Edit</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400">
                                        <svg class="mx-auto h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                        <p class="mt-2 text-xs font-semibold text-slate-600">Tidak ada data karyawan yang cocok</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                @if($employees->hasPages() || $employees->total() > 0)
                    <div class="px-4 py-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                        <div>
                            Menampilkan <span class="font-semibold text-slate-800">{{ $employees->firstItem() ?? 0 }}</span> sampai 
                            <span class="font-semibold text-slate-800">{{ $employees->lastItem() ?? 0 }}</span> dari 
                            <span class="font-semibold text-slate-800">{{ $employees->total() }}</span> total karyawan
                        </div>
                        
                        <div>
                            {{ $employees->links('pagination::tailwind') }}
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </div>

</div>

<!-- Modal Edit Employee -->
<div id="editEmployeeModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity" aria-modal="true" role="dialog">
    <div class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl border border-slate-200 transition-all flex flex-col space-y-4">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900">Perbarui Data Karyawan</h3>
            <button type="button" onclick="closeEditEmployeeModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>

        <form id="editEmployeeForm" method="POST" class="space-y-4 m-0">
            @csrf
            @method('PUT')
            
            <div>
                <label for="edit_kode_karyawan" class="block text-xs font-semibold text-slate-700 mb-1">
                    Kode Karyawan (User ID) <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="kode_karyawan" id="edit_kode_karyawan" required
                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
            </div>

            <div>
                <label for="edit_nama" class="block text-xs font-semibold text-slate-700 mb-1">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama" id="edit_nama" required
                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
            </div>

            <div>
                <label for="edit_departemen" class="block text-xs font-semibold text-slate-700 mb-1">
                    Cabang / Departemen <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="departemen" id="edit_departemen" required
                       class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
            </div>

            <div>
                <label for="edit_aktif" class="block text-xs font-semibold text-slate-700 mb-1">
                    Status Hak Akses Presensi <span class="text-rose-500">*</span>
                </label>
                <select name="aktif" id="edit_aktif" required
                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 py-2 px-3 text-xs text-slate-900 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all">
                    <option value="1">Aktif (Bisa melakukan presensi publik)</option>
                    <option value="0">Nonaktif (Resign / Cuti Panjang)</option>
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

@endsection

@section('scripts')
<script>
    const editEmployeeModal = document.getElementById('editEmployeeModal');
    const editEmployeeForm = document.getElementById('editEmployeeForm');

    function openEditEmployeeModal(employee) {
        editEmployeeForm.action = `/admin/karyawan/${employee.id}`;

        document.getElementById('edit_kode_karyawan').value = employee.kode_karyawan;
        document.getElementById('edit_nama').value = employee.nama;
        document.getElementById('edit_departemen').value = employee.departemen;
        document.getElementById('edit_aktif').value = employee.aktif ? '1' : '0';

        editEmployeeModal.classList.remove('hidden');
    }

    function closeEditEmployeeModal() {
        editEmployeeModal.classList.add('hidden');
    }

    window.addEventListener('click', function(e) {
        if (e.target === editEmployeeModal) {
            closeEditEmployeeModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !editEmployeeModal.classList.contains('hidden')) {
            closeEditEmployeeModal();
        }
    });
</script>
@endsection
