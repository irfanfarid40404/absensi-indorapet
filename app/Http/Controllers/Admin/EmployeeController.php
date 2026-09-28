<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the employees with search and pagination.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $departemenFilter = $request->input('departemen', 'ALL');
        $statusFilter = $request->input('status', 'ALL');
        $perPage = (int) $request->input('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        $query = Employee::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode_karyawan', 'like', "%{$search}%");
            });
        }

        if ($departemenFilter !== 'ALL') {
            $query->where('departemen', $departemenFilter);
        }

        if ($statusFilter === 'active') {
            $query->where('aktif', true);
        } elseif ($statusFilter === 'inactive') {
            $query->where('aktif', false);
        }

        // Stats
        $totalEmployees = Employee::count();
        $totalActive = Employee::where('aktif', true)->count();
        $totalInactive = Employee::where('aktif', false)->count();

        $employees = $query->orderBy('aktif', 'desc')
            ->orderBy('nama', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        $departments = Employee::distinct()->pluck('departemen')->sort();

        return view('admin.karyawan.index', compact(
            'employees',
            'departments',
            'search',
            'departemenFilter',
            'statusFilter',
            'perPage',
            'totalEmployees',
            'totalActive',
            'totalInactive'
        ));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_karyawan' => 'required|string|unique:employees,kode_karyawan|max:50',
            'nama' => 'required|string|max:150',
            'departemen' => 'required|string|max:100',
        ]);

        Employee::create([
            'kode_karyawan' => $request->kode_karyawan,
            'nama' => $request->nama,
            'departemen' => $request->departemen,
            'aktif' => true,
        ]);

        return redirect()->route('admin.karyawan')
            ->with('success', "Karyawan {$request->nama} berhasil ditambahkan.");
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'kode_karyawan' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employees', 'kode_karyawan')->ignore($employee->id),
            ],
            'nama' => 'required|string|max:150',
            'departemen' => 'required|string|max:100',
            'aktif' => 'required|boolean',
        ]);

        $employee->update([
            'kode_karyawan' => $request->kode_karyawan,
            'nama' => $request->nama,
            'departemen' => $request->departemen,
            'aktif' => $request->aktif,
        ]);

        return redirect()->route('admin.karyawan')
            ->with('success', "Data karyawan {$request->nama} berhasil diperbarui.");
    }
}
