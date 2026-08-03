<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;
    private Employee $activeEmployee;
    private Employee $inactiveEmployee;

    protected function setUp(): void
    {
        parent::setUp();

        // Create default admin
        $this->admin = Admin::create([
            'nama' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);

        // Create sample employees
        $this->activeEmployee = Employee::create([
            'kode_karyawan' => 'EMP01',
            'nama' => 'Budi Active',
            'departemen' => 'INDORAPET2',
            'aktif' => true,
        ]);

        $this->inactiveEmployee = Employee::create([
            'kode_karyawan' => 'EMP02',
            'nama' => 'Siti Inactive',
            'departemen' => 'INDORAPET4',
            'aktif' => false,
        ]);
    }

    /**
     * Test public index page load.
     */
    public function test_public_index_loads_only_active_employees(): void
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('Budi Active');
        $response->assertDontSee('Siti Inactive');
    }

    /**
     * Test clock-in flow.
     */
    public function test_employee_can_clock_in(): void
    {
        $response = $this->post('/absen-masuk', [
            'employee_id' => $this->activeEmployee->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $this->activeEmployee->id,
            'tanggal' => Carbon::today()->toDateString(),
        ]);
        
        $attendance = Attendance::first();
        $this->assertNotNull($attendance->jam_masuk);
        $this->assertNull($attendance->jam_keluar);
    }

    /**
     * Test duplicate clock-in is prevented.
     */
    public function test_employee_cannot_clock_in_twice_in_a_day(): void
    {
        // First clock in
        Attendance::create([
            'employee_id' => $this->activeEmployee->id,
            'tanggal' => Carbon::today()->toDateString(),
            'jam_masuk' => '08:00:00',
        ]);

        // Try second clock in
        $response = $this->post('/absen-masuk', [
            'employee_id' => $this->activeEmployee->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertEquals(1, Attendance::count());
    }

    /**
     * Test clock-out without clock-in is blocked.
     */
    public function test_employee_cannot_clock_out_without_clocking_in(): void
    {
        $response = $this->post('/absen-pulang', [
            'employee_id' => $this->activeEmployee->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('attendances', [
            'employee_id' => $this->activeEmployee->id,
        ]);
    }

    /**
     * Test clock-out flow works after clock-in.
     */
    public function test_employee_can_clock_out_after_clocking_in(): void
    {
        // Clock in first
        Attendance::create([
            'employee_id' => $this->activeEmployee->id,
            'tanggal' => Carbon::today()->toDateString(),
            'jam_masuk' => '08:00:00',
        ]);

        // Clock out
        $response = $this->post('/absen-pulang', [
            'employee_id' => $this->activeEmployee->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $attendance = Attendance::first();
        $this->assertNotNull($attendance->jam_keluar);
    }

    /**
     * Test admin dashboard authorization.
     */
    public function test_guests_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Test admin login and dashboard access.
     */
    public function test_admin_can_login_and_access_dashboard(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();

        $dashboardResponse = $this->actingAs($this->admin)->get('/admin/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Rekap Absensi Karyawan');
    }

    /**
     * Test admin can perform employee CRUD actions.
     */
    public function test_admin_can_manage_employees(): void
    {
        // Add new employee
        $response = $this->actingAs($this->admin)->post('/admin/karyawan', [
            'kode_karyawan' => 'EMP99',
            'nama' => 'Agus Budiman',
            'departemen' => 'INDORAPET2',
        ]);

        $response->assertRedirect(route('admin.karyawan'));
        $this->assertDatabaseHas('employees', [
            'kode_karyawan' => 'EMP99',
            'nama' => 'Agus Budiman',
            'aktif' => true,
        ]);

        $employee = Employee::where('kode_karyawan', 'EMP99')->first();

        // Update/soft-disable employee
        $updateResponse = $this->actingAs($this->admin)->put("/admin/karyawan/{$employee->id}", [
            'kode_karyawan' => 'EMP99-UPDATED',
            'nama' => 'Agus Budiman Updated',
            'departemen' => 'INDORAPET4',
            'aktif' => '0', // disable
        ]);

        $updateResponse->assertRedirect(route('admin.karyawan'));
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'kode_karyawan' => 'EMP99-UPDATED',
            'aktif' => false,
        ]);
    }

    /**
     * Test admin can download weekly Excel report.
     */
    public function test_admin_can_export_weekly_report(): void
    {
        $todayStr = Carbon::today()->toDateString();
        
        $response = $this->actingAs($this->admin)->get("/admin/export/mingguan?dari={$todayStr}&sampai={$todayStr}");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        
        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('attachment;', $contentDisposition);
        $this->assertStringContainsString('filename=catatan_kehadiran_karyawan_', $contentDisposition);
    }

    /**
     * Test weekly export orders employees by ID (kode_karyawan / id).
     */
    public function test_weekly_export_orders_employees_by_id(): void
    {
        // Create employees out of ID order with different departments
        Employee::create(['kode_karyawan' => '10', 'nama' => 'Zack', 'departemen' => 'A_DEPT', 'aktif' => true]);
        Employee::create(['kode_karyawan' => '2', 'nama' => 'Adam', 'departemen' => 'Z_DEPT', 'aktif' => true]);
        Employee::create(['kode_karyawan' => '1', 'nama' => 'Charlie', 'departemen' => 'B_DEPT', 'aktif' => true]);

        $todayStr = Carbon::today()->toDateString();
        $response = $this->actingAs($this->admin)->get("/admin/export/mingguan?dari={$todayStr}&sampai={$todayStr}");

        $response->assertStatus(200);

        // Parse generated Excel content
        $stream = $response->streamedContent();
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_test');
        file_put_contents($tempFile, $stream);

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($tempFile);
        $sheet = $spreadsheet->getActiveSheet();

        // Extract employee user IDs from the spreadsheet cells
        $foundUserIds = [];
        for ($row = 5; $row <= 100; $row++) {
            $val = $sheet->getCell("A{$row}")->getValue();
            if (is_string($val) && str_starts_with($val, 'User ID: ')) {
                $foundUserIds[] = str_replace('User ID: ', '', $val);
            }
        }
        unlink($tempFile);

        // Employee '1', '2', '10' (plus setup's 'EMP01') should be naturally sorted
        $this->assertContains('1', $foundUserIds);
        $this->assertContains('2', $foundUserIds);
        $this->assertContains('10', $foundUserIds);
        
        $idx1 = array_search('1', $foundUserIds);
        $idx2 = array_search('2', $foundUserIds);
        $idx10 = array_search('10', $foundUserIds);

        $this->assertTrue($idx1 < $idx2, 'ID 1 should appear before ID 2');
        $this->assertTrue($idx2 < $idx10, 'ID 2 should appear before ID 10');
    }

    /**
     * Test admin can download daily detailed Excel report for an employee.
     */
    public function test_admin_can_export_daily_report(): void
    {
        $todayStr = Carbon::today()->toDateString();
        
        $response = $this->actingAs($this->admin)->get("/admin/export/rincian/{$this->activeEmployee->id}?dari={$todayStr}&sampai={$todayStr}");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        
        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('attachment;', $contentDisposition);
        $this->assertStringContainsString('filename=rincian_harian_', $contentDisposition);
    }

    /**
     * Test weekly export formats late duration into hours & mins and colors late cells red.
     */
    public function test_weekly_export_formats_late_time_in_hours_and_mins_with_red_style(): void
    {
        $todayStr = Carbon::today()->toDateString();
        
        // Create 75 minutes late attendance (09:15 vs 08:00 standard)
        Attendance::create([
            'employee_id' => $this->activeEmployee->id,
            'tanggal' => $todayStr,
            'jam_masuk' => '09:15:00',
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/export/mingguan?dari={$todayStr}&sampai={$todayStr}");
        $response->assertStatus(200);

        $stream = $response->streamedContent();
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_test');
        file_put_contents($tempFile, $stream);

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($tempFile);
        $sheet = $spreadsheet->getActiveSheet();

        // Cell A7 is clock in/out for first employee on date 1
        $cellVal = $sheet->getCell('A7')->getValue();
        $this->assertStringContainsString('(T:1j 15m)', $cellVal);

        // Verify late cell font color is Red (FFDC2626)
        $fontColor = $sheet->getStyle('A7')->getFont()->getColor()->getARGB();
        $this->assertEquals('FFDC2626', $fontColor);

        unlink($tempFile);
    }
}
