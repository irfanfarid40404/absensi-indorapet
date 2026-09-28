<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display the public attendance page.
     */
    public function index()
    {
        $today = Carbon::today()->toDateString();
        
        // Load active employees sorted by name
        $employees = Employee::where('aktif', true)->orderBy('nama')->get();
        
        // Load today's attendances mapped by employee_id for status indicators
        $todayAttendances = Attendance::where('tanggal', $today)
            ->whereNotNull('jam_masuk')
            ->get()
            ->keyBy('employee_id');
            
        $departments = $employees->pluck('departemen')->unique()->sort()->values();
        $jamMasukStandar = \App\Models\Setting::getValue('jam_masuk_standar', '08:00');

        return view('attendance.index', compact('employees', 'todayAttendances', 'departments', 'jamMasukStandar'));
    }

    /**
     * Process check-in (Absen Masuk).
     */
    public function masuk(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employee = Employee::where('id', $request->employee_id)->where('aktif', true)->firstOrFail();
        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->toTimeString();

        // Check if attendance already exists for today
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('tanggal', $today)
            ->first();

        if ($attendance && $attendance->jam_masuk !== null) {
            return redirect()->back()->with('error', "{$employee->nama} sudah absen masuk hari ini pada pukul " . Carbon::parse($attendance->jam_masuk)->format('H:i') . ".");
        }

        if ($attendance) {
            $attendance->update([
                'jam_masuk' => $nowTime,
            ]);
        } else {
            Attendance::create([
                'employee_id' => $employee->id,
                'tanggal' => $today,
                'jam_masuk' => $nowTime,
            ]);
        }

        return redirect()->back()->with('success', "Absen masuk berhasil untuk {$employee->nama} pada pukul " . Carbon::parse($nowTime)->format('H:i') . ".");
    }

    /**
     * Process check-out (Absen Pulang).
     */
    public function pulang(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employee = Employee::where('id', $request->employee_id)->where('aktif', true)->firstOrFail();
        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->toTimeString();

        // Check if attendance exists for today
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('tanggal', $today)
            ->first();

        if (!$attendance || $attendance->jam_masuk === null) {
            return redirect()->back()->with('error', "{$employee->nama} belum absen masuk hari ini. Silakan absen masuk terlebih dahulu.");
        }

        // Overwrite or set jam_keluar with current time
        $attendance->update([
            'jam_keluar' => $nowTime,
        ]);

        return redirect()->back()->with('success', "Absen pulang berhasil untuk {$employee->nama} pada pukul " . Carbon::parse($nowTime)->format('H:i') . ".");
    }
}
