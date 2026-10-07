<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Orkestrasi seeding: 15 mahasiswa acak (masing-masing 2 project) dan 1 akun demo penguji.
 *
 * Aman dijalankan berulang kali tanpa error duplikasi.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            StudentSeeder::class,
            DemoUserSeeder::class,
        ]);
    }
}
