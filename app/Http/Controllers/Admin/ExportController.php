<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportController extends Controller
{
    /**
     * Export weekly calendar-style grid of attendance for all active employees.
     */
    public function exportMingguan(Request $request)
    {
        $request->validate([
            'dari' => 'required|date',
            'sampai' => 'required|date',
        ]);

        $dari = Carbon::parse($request->dari);
        $sampai = Carbon::parse($request->sampai);

        // Max 7 days limit
        if ($dari->diffInDays($sampai) >= 7) {
            return redirect()->back()->with('error', 'Rentang tanggal untuk export mingguan maksimal adalah 7 hari.');
        }

        // Generate date range
        $period = CarbonPeriod::create($dari, $sampai);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->toDateString();
        }

        $employees = Employee::where('aktif', true)
            ->get()
            ->sortBy([
                ['kode_karyawan', 'asc'],
                ['id', 'asc'],
            ], SORT_NATURAL);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Catatan Kehadiran');

        // Document title and headers
        $sheet->setCellValue('A1', 'Catatan Kehadiran Karyawan');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

        $sheet->setCellValue('A2', "Tanggal Kehadiran: {$dari->format('Y/m/d')} ~ {$sampai->format('Y/m/d')}");
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(11);

        $sheet->setCellValue('A3', 'Tanggal Dibuat: ' . Carbon::now()->format('Y/m/d H:i'));
        $sheet->getStyle('A3')->getFont()->setSize(10)->getColor()->setARGB('FF666666');

        // Thin border style
        $thinBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCCCCCC'],
                ],
            ],
        ];

        // Start drawing blocks from row 5
        $row = 5;
        foreach ($employees as $employee) {
            // Fetch attendance for this employee in the range
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
                ->get()
                ->keyBy(function ($item) {
                    return $item->tanggal->toDateString();
                });

            // Block Row 1: Employee Header Info (Black background with bold white text)
            $sheet->setCellValue("A{$row}", "User ID: {$employee->kode_karyawan}");
            $sheet->setCellValue("C{$row}", "Nama: {$employee->nama}");
            $sheet->setCellValue("F{$row}", "Departemen: {$employee->departemen}");
            
            // Format employee info row
            $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle("A{$row}:G{$row}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF000000'); // Solid Black background

            // Block Row 2: Day Numbers
            $colIdx = 1;
            foreach ($dates as $dateStr) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                $dayNum = Carbon::parse($dateStr)->day;
                $sheet->setCellValue("{$colLetter}" . ($row + 1), $dayNum);
                $colIdx++;
            }

            // Style day numbers row (Dark header background with bold white text)
            $colMaxLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($dates));
            $sheet->getStyle("A" . ($row + 1) . ":{$colMaxLetter}" . ($row + 1))->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle("A" . ($row + 1) . ":{$colMaxLetter}" . ($row + 1))
                ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A" . ($row + 1) . ":{$colMaxLetter}" . ($row + 1))
                ->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF1E293B'); // Dark slate/black header background

            // Load setting jam masuk standar
            $jamMasukStandar = Setting::getValue('jam_masuk_standar', '08:00');

            // Block Row 3: Clock In & Clock Out
            $colIdx = 1;
            foreach ($dates as $dateStr) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                $cellCoord = "{$colLetter}" . ($row + 2);

                if (isset($attendances[$dateStr])) {
                    $att = $attendances[$dateStr];
                    if ($att->keterangan) {
                        $sheet->setCellValue($cellCoord, $att->keterangan);
                    } else {
                        $inTime = $att->jam_masuk ? Carbon::parse($att->jam_masuk)->format('H:i') : '-';
                        $isLate = false;

                        if ($att->jam_masuk) {
                            $inTimeCarbon = Carbon::parse('2000-01-01 ' . Carbon::parse($att->jam_masuk)->format('H:i:s'));
                            $stdTimeCarbon = Carbon::parse('2000-01-01 ' . Carbon::parse($jamMasukStandar)->format('H:i:s'));
                            
                            if ($inTimeCarbon->greaterThan($stdTimeCarbon)) {
                                $isLate = true;
                                $diffMin = abs((int) $inTimeCarbon->diffInMinutes($stdTimeCarbon));
                                $hours = floor($diffMin / 60);
                                $mins = $diffMin % 60;
                                if ($hours > 0) {
                                    $lateFormatted = $mins > 0 ? "{$hours}j {$mins}m" : "{$hours}j";
                                } else {
                                    $lateFormatted = "{$mins}m";
                                }
                                $inTime .= " (T:{$lateFormatted})";
                            }
                        }

                        $outTime = $att->jam_keluar ? Carbon::parse($att->jam_keluar)->format('H:i') : '-';
                        $sheet->setCellValue($cellCoord, "{$inTime}\n{$outTime}");

                        // Highlight late attendance in red text & soft red background
                        if ($isLate) {
                            $sheet->getStyle($cellCoord)->getFont()->setBold(true)->getColor()->setARGB('FFDC2626');
                            $sheet->getStyle($cellCoord)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB('FFFEE2E2');
                        }
                    }
                } else {
                    $sheet->setCellValue($cellCoord, "-");
                }
                $colIdx++;
            }

            // Style clock times row (Wrap text & center)
            $sheet->getStyle("A" . ($row + 2) . ":{$colMaxLetter}" . ($row + 2))
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            
            $sheet->getRowDimension($row + 2)->setRowHeight(32);

            // Apply borders to the grid portion of this employee block (Rows +1 and +2)
            $sheet->getStyle("A" . ($row + 1) . ":{$colMaxLetter}" . ($row + 2))->applyFromArray($thinBorder);

            // Jump to the next block (leave a blank row)
            $row += 4;
        }

        // Auto size columns
        for ($i = 1; $i <= max(7, count($dates)); $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setWidth(14);
        }

        // Export as attachment
        $fileName = 'catatan_kehadiran_karyawan_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export daily detailed report for a single employee.
     */
    public function exportRincian(Request $request, Employee $employee)
    {
        $request->validate([
            'dari' => 'required|date',
            'sampai' => 'required|date',
        ]);

        $dari = Carbon::parse($request->dari);
        $sampai = Carbon::parse($request->sampai);

        // Fetch attendance records
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->get()
            ->keyBy(function ($item) {
                return $item->tanggal->toDateString();
            });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rincian Harian');

        // Main Title
        $sheet->setCellValue('A1', 'Laporan Rincian Harian Kehadiran Karyawan');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Header Metadata info
        $sheet->setCellValue('A3', 'Nama:');
        $sheet->setCellValue('B3', $employee->nama);
        $sheet->getStyle('A3')->getFont()->setBold(true);

        $sheet->setCellValue('A4', 'Departemen:');
        $sheet->setCellValue('B4', $employee->departemen);
        $sheet->getStyle('A4')->getFont()->setBold(true);

        $sheet->setCellValue('A5', 'Periode:');
        $sheet->setCellValue('B5', "{$dari->format('d/m/Y')} ~ {$sampai->format('d/m/Y')}");
        $sheet->getStyle('A5')->getFont()->setBold(true);

        $sheet->setCellValue('A6', 'Tanggal Dibuat:');
        $sheet->setCellValue('B6', Carbon::now()->format('d/m/Y H:i'));
        $sheet->getStyle('A6')->getFont()->setBold(true);

        // Table headers (Row 8)
        $headers = ['Tanggal', 'Hari', 'Jam Masuk', 'Keterlambatan', 'Jam Keluar', 'Durasi Kerja', 'Keterangan'];
        $colIdx = 1;
        foreach ($headers as $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $sheet->setCellValue("{$colLetter}8", $header);
            $colIdx++;
        }

        // Style headers
        $sheet->getStyle('A8:G8')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A8:G8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F172A'); // dark slate
        $sheet->getStyle('A8:G8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Populate rows starting from row 9
        $row = 9;
        $period = CarbonPeriod::create($dari, $sampai);
        
        // Use Indonesian locale for day names
        Carbon::setLocale('id');

        $thinBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFDDDDDD'],
                ],
            ],
        ];

        // Load setting jam masuk standar
        $jamMasukStandar = Setting::getValue('jam_masuk_standar', '08:00');

        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            
            $sheet->setCellValue("A{$row}", $date->format('d/m/Y'));
            $sheet->setCellValue("B{$row}", $date->locale('id')->translatedFormat('l')); // Hari name e.g. Senin, Selasa

            if (isset($attendances[$dateStr])) {
                $att = $attendances[$dateStr];
                
                $inTime = $att->jam_masuk ? Carbon::parse($att->jam_masuk)->format('H:i') : '-';
                $outTime = $att->jam_keluar ? Carbon::parse($att->jam_keluar)->format('H:i') : '-';
                
                $lateStr = '-';
                $isLate = false;
                if ($att->jam_masuk && !$att->keterangan) {
                    $inTimeCarbon = Carbon::parse('2000-01-01 ' . Carbon::parse($att->jam_masuk)->format('H:i:s'));
                    $stdTimeCarbon = Carbon::parse('2000-01-01 ' . Carbon::parse($jamMasukStandar)->format('H:i:s'));
                    
                    if ($inTimeCarbon->greaterThan($stdTimeCarbon)) {
                        $isLate = true;
                        $diffMin = $inTimeCarbon->diffInMinutes($stdTimeCarbon);
                        $hours = floor($diffMin / 60);
                        $mins = $diffMin % 60;
                        if ($hours > 0) {
                            $lateStr = $mins > 0 ? "{$hours} jam {$mins} menit" : "{$hours} jam";
                        } else {
                            $lateStr = "{$mins} menit";
                        }
                    }
                }

                $duration = '-';
                if ($att->jam_masuk && $att->jam_keluar) {
                    $in = Carbon::parse($att->jam_masuk);
                    $out = Carbon::parse($att->jam_keluar);
                    $diff = $in->diff($out);
                    $duration = sprintf('%02d:%02d', $diff->h, $diff->i);
                }

                $sheet->setCellValue("C{$row}", $inTime);
                $sheet->setCellValue("D{$row}", $lateStr);
                $sheet->setCellValue("E{$row}", $outTime);
                $sheet->setCellValue("F{$row}", $duration);
                $sheet->setCellValue("G{$row}", $att->keterangan ?: '-');

                if ($isLate) {
                    $sheet->getStyle("D{$row}")->getFont()->setBold(true)->getColor()->setARGB('FFDC2626');
                    $sheet->getStyle("D{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFEE2E2');
                }
            } else {
                // If there's no log at all for this date
                $sheet->setCellValue("C{$row}", '-');
                $sheet->setCellValue("D{$row}", '-');
                $sheet->setCellValue("E{$row}", '-');
                $sheet->setCellValue("F{$row}", '-');
                $sheet->setCellValue("G{$row}", 'Alpha'); // Default to Alpha if absent
            }

            // Alignments
            $sheet->getStyle("A{$row}:F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // Borders
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray($thinBorder);

            $row++;
        }

        // Auto size columns
        for ($i = 1; $i <= 7; $i++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Export as attachment
        $fileName = 'rincian_harian_' . str_replace(' ', '_', strtolower($employee->nama)) . '_' . Carbon::now()->format('Ymd_His') . '.xlsx';
        
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
