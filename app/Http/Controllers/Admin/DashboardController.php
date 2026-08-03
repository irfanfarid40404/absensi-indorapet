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

        // Query builder
        $query = Attendance::with('employee')
            ->whereBetween('tanggal', [$dari, $sampai]);

        if ($departemenFilter !== 'ALL') {
            $query->whereHas('employee', function ($q) use ($departemenFilter) {
                $q->where('departemen', $departemenFilter);
            });
        }

        // Sort by tanggal desc, employee name asc
        $attendances = $query->join('employees', 'attendances.employee_id', '=', 'employees.id')
            ->select('attendances.*')
            ->orderBy('attendances.tanggal', 'desc')
            ->orderBy('employees.nama', 'asc')
            ->get();

        // Get list of active departments for filter dropdown
        $departments = Employee::distinct()->pluck('departemen')->sort();

        // Get list of all employees for manual entry/edit modal
        $employees = Employee::orderBy('nama')->get();

        // Load jam masuk standar setting
        $jamMasukStandar = Setting::getValue('jam_masuk_standar', '08:00');

        return view('admin.dashboard', compact('attendances', 'departments', 'employees', 'dari', 'sampai', 'departemenFilter', 'jamMasukStandar'));
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
