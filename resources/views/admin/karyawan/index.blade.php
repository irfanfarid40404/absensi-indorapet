@extends('layouts.app')

@section('title', 'Kelola Data Karyawan - Indorapet')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade-in">
    
    <!-- Left Sidebar: Add Employee Form -->
    <div class="space-y-6">
        <div>
            <span class="inline-block rounded-xl bg-[#FF66C4] px-3 py-1 text-[11px] font-black text-black border-2 border-black shadow-[2px_2px_0px_0px_#000] uppercase tracking-wider mb-2">
                👥 KARYAWAN MANAGEMENT
            </span>
            <h2 class="text-2xl font-black text-black uppercase tracking-tight">Kelola Karyawan</h2>
            <p class="text-xs font-bold text-black uppercase tracking-wide">Tambah dan perbarui data karyawan Toko Indorapet.</p>
        </div>
        
        <div class="bg-[#FFF9E6] rounded-3xl p-6 border-3 border-black shadow-[6px_6px_0px_0px_#000]">
            <h3 class="font-black text-black text-sm uppercase tracking-wider mb-4 border-b-2 border-black pb-2">Tambah Karyawan Baru</h3>
            
            <form method="POST" action="{{ route('admin.karyawan.store') }}" class="space-y-4 m-0">
                @csrf
                
                <div>
                    <label for="kode_karyawan" class="block text-xs font-black text-black uppercase tracking-wider mb-1">
                        Kode Karyawan (User ID)
                    </label>
                    <input type="text" name="kode_karyawan" id="kode_karyawan" required value="{{ old('kode_karyawan') }}"
                        placeholder="Contoh: 101"
                        class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
                    @error('kode_karyawan')
                        <p class="mt-1 text-xs font-black text-[#FF4747]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama" class="block text-xs font-black text-black uppercase tracking-wider mb-1">
                        Nama Lengkap
                    </label>
                    <input type="text" name="nama" id="nama" required value="{{ old('nama') }}"
                        placeholder="Contoh: Ahmad Subardjo"
                        class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
                    @error('nama')
                        <p class="mt-1 text-xs font-black text-[#FF4747]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="departemen" class="block text-xs font-black text-black uppercase tracking-wider mb-1">
                        Cabang / Departemen
                    </label>
                    <input type="text" name="departemen" id="departemen" required value="{{ old('departemen') }}"
                        placeholder="Contoh: INDORAPET2"
                        class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
                    @error('departemen')
                        <p class="mt-1 text-xs font-black text-[#FF4747]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="w-full flex justify-center rounded-2xl bg-[#54EA54] py-3 px-4 text-xs font-black text-black uppercase tracking-wider border-3 border-black shadow-[4px_4px_0px_0px_#000] hover:bg-[#FFE600] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_#000] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[2px_2px_0px_0px_#000] transition-all">
                        ➕ Simpan Karyawan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Content: Employee List Table -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-3xl border-3 border-black shadow-[8px_8px_0px_0px_#000] overflow-hidden">
            <div class="p-6 border-b-3 border-black bg-[#FFF9E6]">
                <h3 class="font-black text-black text-sm uppercase tracking-wider">Daftar Semua Karyawan</h3>
                <p class="text-xs font-bold text-black uppercase mt-1">Karyawan nonaktif disembunyikan dari daftar absensi publik.</p>
            </div>
            
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-black text-white border-b-3 border-black">
                            <th class="p-4 text-xs font-black uppercase tracking-wider">User ID</th>
                            <th class="p-4 text-xs font-black uppercase tracking-wider">Nama</th>
                            <th class="p-4 text-xs font-black uppercase tracking-wider">Departemen</th>
                            <th class="p-4 text-xs font-black uppercase tracking-wider">Status</th>
                            <th class="p-4 text-xs font-black uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-black/10 text-sm">
                        @forelse($employees as $emp)
                            <tr class="hover:bg-[#FFF9E6] transition-colors">
                                <td class="p-4">
                                    <span class="inline-block bg-[#FFE600] text-black font-mono font-bold text-xs px-2 py-0.5 rounded border border-black shadow-[1.5px_1.5px_0px_0px_#000]">
                                        {{ $emp->kode_karyawan }}
                                    </span>
                                </td>
                                <td class="p-4 font-black text-black">
                                    {{ $emp->nama }}
                                </td>
                                <td class="p-4">
                                    <span class="inline-block bg-white text-black border border-black font-extrabold text-xs px-2 py-0.5 rounded-md shadow-[1.5px_1.5px_0px_0px_#000]">
                                        {{ $emp->departemen }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    @if($emp->aktif)
                                        <span class="inline-block bg-[#54EA54] text-black border border-black px-2.5 py-0.5 text-xs font-black rounded-md shadow-[1.5px_1.5px_0px_0px_#000]">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-block bg-slate-200 text-black border border-black px-2.5 py-0.5 text-xs font-black rounded-md shadow-[1.5px_1.5px_0px_0px_#000]">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <button type="button" 
                                        onclick="openEditEmployeeModal({{ json_encode($emp) }})"
                                        class="rounded-xl bg-[#00F0FF] p-2 text-black border border-black font-black shadow-[2px_2px_0px_0px_#000] hover:bg-[#FFE600] transition-all"
                                        title="Edit Karyawan">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-black font-black uppercase tracking-wider">
                                    Tidak ada data karyawan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Employee -->
<div id="editEmployeeModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in" aria-modal="true" role="dialog">
    <div class="w-full max-w-md transform overflow-hidden rounded-3xl bg-[#FFFDF5] p-6 sm:p-8 shadow-[12px_12px_0px_0px_#000] border-4 border-black flex flex-col space-y-4">
        
        <div class="flex items-center justify-between border-b-3 border-black pb-3">
            <h3 class="text-base font-black text-black uppercase tracking-tight">Perbarui Data Karyawan</h3>
            <button type="button" onclick="closeEditEmployeeModal()" class="p-1 rounded-xl bg-white border-2 border-black text-black hover:bg-[#FF4747] hover:text-white shadow-[2px_2px_0px_0px_#000] transition-colors">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>
        </div>

        <form id="editEmployeeForm" method="POST" class="space-y-4 m-0">
            @csrf
            @method('PUT')
            
            <div>
                <label for="edit_kode_karyawan" class="block text-xs font-black text-black uppercase tracking-wider mb-1">
                    Kode Karyawan (User ID)
                </label>
                <input type="text" name="kode_karyawan" id="edit_kode_karyawan" required
                    class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
            </div>

            <div>
                <label for="edit_nama" class="block text-xs font-black text-black uppercase tracking-wider mb-1">
                    Nama Lengkap
                </label>
                <input type="text" name="nama" id="edit_nama" required
                    class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
            </div>

            <div>
                <label for="edit_departemen" class="block text-xs font-black text-black uppercase tracking-wider mb-1">
                    Cabang / Departemen
                </label>
                <input type="text" name="departemen" id="edit_departemen" required
                    class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
            </div>

            <div>
                <label for="edit_aktif" class="block text-xs font-black text-black uppercase tracking-wider mb-1">
                    Status Aktif
                </label>
                <select name="aktif" id="edit_aktif" required
                    class="block w-full rounded-xl border-2 border-black py-2.5 px-3 text-black font-extrabold text-xs bg-white focus:outline-none focus:shadow-[3px_3px_0px_0px_#000] transition-all">
                    <option value="1">Aktif (Muncul di absensi publik)</option>
                    <option value="0">Nonaktif (Resign / Cuti Panjang)</option>
                </select>
            </div>

            <div class="pt-2">
                <button type="submit" 
                    class="w-full flex justify-center rounded-2xl bg-[#00F0FF] py-3 px-4 text-xs font-black text-black uppercase tracking-wider border-3 border-black shadow-[4px_4px_0px_0px_#000] hover:bg-[#FFE600] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_#000] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[2px_2px_0px_0px_#000] transition-all">
                    💾 Simpan Perubahan
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
        // Set action URI
        editEmployeeForm.action = `/admin/karyawan/${employee.id}`;

        // Populate fields
        document.getElementById('edit_kode_karyawan').value = employee.kode_karyawan;
        document.getElementById('edit_nama').value = employee.nama;
        document.getElementById('edit_departemen').value = employee.departemen;
        document.getElementById('edit_aktif').value = employee.aktif ? '1' : '0';

        // Open modal
        editEmployeeModal.classList.remove('hidden');
        editEmployeeModal.classList.add('flex');
    }

    function closeEditEmployeeModal() {
        editEmployeeModal.classList.add('hidden');
        editEmployeeModal.classList.remove('flex');
    }

    window.addEventListener('click', function(e) {
        if (e.target === editEmployeeModal) {
            closeEditEmployeeModal();
        }
    });
</script>
@endsection
