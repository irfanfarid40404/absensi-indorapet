<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed default admin
        Admin::create([
            'nama' => 'Admin Indorapet',
            'email' => 'admin@indorapet.com',
            'password' => Hash::make('password'),
        ]);

        // Seed sample employees
        // Employee::insert([
        //     [
        //         'kode_karyawan' => '1',
        //         'nama' => 'Budi Santoso',
        //         'departemen' => 'INDORAPET2',
        //         'aktif' => true,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'kode_karyawan' => '2',
        //         'nama' => 'Siti Aminah',
        //         'departemen' => 'INDORAPET4',
        //         'aktif' => true,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'kode_karyawan' => '3',
        //         'nama' => 'Joko Susilo',
        //         'departemen' => 'INDORAPET2',
        //         'aktif' => true,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'kode_karyawan' => '4',
        //         'nama' => 'Rini Lestari',
        //         'departemen' => 'INDORAPET4',
        //         'aktif' => true,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'kode_karyawan' => '5',
        //         'nama' => 'Dewi Safitri',
        //         'departemen' => 'INDORAPET2',
        //         'aktif' => false, // Inactive employee (resigned/off)
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        // ]);

        // Seed default settings
        Setting::setValue('jam_masuk_standar', '08:00');
    }
}

