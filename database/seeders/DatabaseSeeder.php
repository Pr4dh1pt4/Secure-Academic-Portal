<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Orkestrasi seeding: 15 mahasiswa (pemilik portofolio + 14 acak, masing-masing 2 project)
 * dan 1 akun demo penguji, total 16 user dan 33 project. Setiap user lalu mendapat
 * 2 sesi obrolan Agentic AI (32 sesi, 192 pesan) beserta log tool-nya.
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
            // Dijalankan terakhir agar semua user di atas ikut mendapat riwayat obrolan.
            ChatSeeder::class,
        ]);
    }
}
