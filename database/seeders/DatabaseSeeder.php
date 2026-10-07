<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Orkestrasi seeding: 15 mahasiswa (pemilik portofolio + 14 acak, masing-masing 2 project)
 * dan 1 akun demo penguji, total 16 user dan 33 project.
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
            // Pemilik portofolio dibuat lebih dulu agar ikut terhitung sebagai salah satu dari 15 mahasiswa.
            PortfolioOwnerSeeder::class,
            StudentSeeder::class,
            DemoUserSeeder::class,
        ]);
    }
}
