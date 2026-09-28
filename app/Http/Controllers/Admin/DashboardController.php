<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with attendance records and filters.
     */
    public function index(Request $request)
    {
        // Default filters: current month range
        $dari = $request->input('dari', Carbon::now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', Carbon::now()->toDateString());
        $departemenFilter = $request->input('departemen', 'ALL');
        $search = $request->input('search', '');
        $perPage = (int) $request->input('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100])) {
            $perPage = 15;
        }

        // Load jam masuk standar setting
        $jamMasukStandar = Setting::getValue('jam_masuk_standar', '08:00');

        // Query builder
        $query = Attendance::with('employee')
            ->join('employees', 'attendances.employee_id', '=', 'employees.id')
            ->select('attendances.*')
            ->whereBetween('attendances.tanggal', [$dari, $sampai]);

        if ($departemenFilter !== 'ALL') {
            $query->where('employees.departemen', $departemenFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('employees.nama', 'like', "%{$search}%")
                  ->orWhere('employees.kode_karyawan', 'like', "%{$search}%");
            });
        }

        // Calculate summary stats on filtered dataset
        $totalRecords = (clone $query)->count();
        $tepatWaktuCount = (clone $query)
            ->whereNotNull('attendances.jam_masuk')
            ->where('attendances.jam_masuk', '<=', $jamMasukStandar . ':00')
            ->count();
        $terlambatCount = (clone $query)
            ->whereNotNull('attendances.jam_masuk')
            ->where('attendances.jam_masuk', '>', $jamMasukStandar . ':00')
            ->count();
        $totalKaryawanAktif = Employee::where('aktif', true)->count();

        // Sort by tanggal desc, employee name asc and paginate
        $attendances = $query->orderBy('attendances.tanggal', 'desc')
            ->orderBy('employees.nama', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        // Get list of active departments for filter dropdown
        $departments = Employee::distinct()->pluck('departemen')->sort();

        // Get list of all employees for manual entry/edit modal
        $employees = Employee::orderBy('nama')->get();

        return view('admin.dashboard', compact(
            'attendances',
            'departments',
            'employees',
            'dari',
            'sampai',
            'departemenFilter',
            'search',
            'perPage',
            'jamMasukStandar',
            'totalRecords',
            'tepatWaktuCount',
            'terlambatCount',
            'totalKaryawanAktif'
        ));
    }

    /**
     * Create or update attendance manually by admin.
     */
    public function updateAttendance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_keluar' => 'nullable|date_format:H:i',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Find or create record
        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'tanggal' => $request->tanggal,
            ],
            [
                'jam_masuk' => $request->jam_masuk ? Carbon::parse($request->jam_masuk)->toTimeString() : null,
                'jam_keluar' => $request->jam_keluar ? Carbon::parse($request->jam_keluar)->toTimeString() : null,
                'keterangan' => $request->keterangan,
            ]
        );

        $employee = Employee::find($request->employee_id);

        return redirect()->back()->with('success', "Absensi manual {$employee->nama} untuk tanggal {$request->tanggal} berhasil diperbarui.");
    }

    /**
     * Update settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'jam_masuk_standar' => 'required|date_format:H:i',
        ]);

        Setting::setValue('jam_masuk_standar', $request->jam_masuk_standar);

        return redirect()->back()->with('success', "Pengaturan jam masuk standar berhasil diperbarui menjadi {$request->jam_masuk_standar}.");
    }
}
