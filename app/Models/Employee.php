<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kode_karyawan', 'nama', 'departemen', 'aktif'])]
class Employee extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }

    /**
     * Get the attendance records for this employee.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
