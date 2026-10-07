<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeder akun demo penguji (dosenpbkk@its.ac.id) dengan 3 proposal portofolio
 * dan riwayat tugas PBKK, siap presentasi.
 *
 * Memakai updateOrCreate sehingga aman dijalankan berulang kali.
 */
class DemoUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Proposal portofolio akun demo, ditulis manual (bukan Faker).
     *
     * Key 'hari_lalu' mengatur tanggal dibuat agar urutan di dashboard rapi.
     *
     * @var list<array{judul: string, deskripsi: string, tema_agent: string, api_key_secure: string, hari_lalu: int}>
     */
    private const PROJECTS = [
        [
            'judul' => 'Platform Agentic AI Otonom untuk Pengujian Aplikasi Web yang Sudah Di-deploy',
            'deskripsi' => 'Agen otonom mengakses aplikasi web yang sudah live, menjalankan pengujian fungsional, '
                .'aksesibilitas, performa, dan keamanan, lalu menelusuri kode terkait di repository Git yang '
                .'terhubung. Agen menyusun perbaikan, memvalidasinya lewat pengujian otomatis agar tidak '
                .'merusak fungsi lain, kemudian membuat pull request terverifikasi untuk direview manusia.',
            'tema_agent' => 'Claude',
            'api_key_secure' => 'sk-demo-qa-agent-7f3c9a1e5b2d4c8f0a6e',
            'hari_lalu' => 1,
        ],
        [
            'judul' => 'Asisten RAG Materi Kuliah PBKK untuk Mahasiswa',
            'deskripsi' => 'RAG agent yang menjawab pertanyaan mahasiswa tentang Laravel (routing, migration, '
                .'Eloquent, middleware) berdasarkan slide dan modul resmi mata kuliah PBKK. Setiap jawaban '
                .'menyertakan rujukan halaman sumber dan dijalankan secara lokal dengan Ollama agar data kuliah '
                .'tidak keluar dari server kampus.',
            'tema_agent' => 'Ollama',
            'api_key_secure' => 'ollama-demo-rag-2b8e4f6a1c3d5e7f9a0b',
            'hari_lalu' => 7,
        ],
        [
            'judul' => 'Multi-Agent Code Reviewer untuk Tugas Laravel',
            'deskripsi' => 'Tim agen CrewAI dengan peran terpisah: reviewer gaya kode (PSR-12), auditor keamanan '
                .'(mass assignment, SQL injection, XSS pada Blade), dan penguji yang menjalankan php artisan test. '
                .'Hasilnya dirangkum menjadi umpan balik terstruktur sebelum tugas dinilai dosen.',
            'tema_agent' => 'CrewAI',
            'api_key_secure' => 'crew-demo-review-9d1f3b5e7a2c4e6f8b0d',
            'hari_lalu' => 14,
        ],
    ];

    /**
     * Riwayat tugas PBKK sepanjang semester. Nilai dikosongkan karena belum diumumkan.
     *
     * @var list<array{mata_kuliah: string, judul: string, deskripsi: string, tautan: string|null, dikumpulkan_pada: string}>
     */
    private const ASSIGNMENTS = [
        [
            'mata_kuliah' => 'Pemrograman Berbasis Kerangka Kerja',
            'judul' => 'Tugas 1: Sistem Informasi Statik Profil Mahasiswa (Kelompok 7)',
            'deskripsi' => 'Website profil akademik ITS berbasis Laravel sesuai PRD kelompok, dengan halaman statis dan routing dasar.',
            'tautan' => 'https://github.com/Renggosakti/PBKK_Tugas-1_Kelompok-7',
            'dikumpulkan_pada' => '2026-09-08',
        ],
        [
            'mata_kuliah' => 'Pemrograman Berbasis Kerangka Kerja',
            'judul' => 'Routing Sandbox Profil Akademik (Individu)',
            'deskripsi' => 'Eksplorasi routing dan controller Laravel: halaman dashboard, profil, data IPK, dan rancangan ide agen.',
            'tautan' => 'https://github.com/Pr4dh1pt4/Website-Pemrograman-Berbasis-Kerangka-Kerja',
            'dikumpulkan_pada' => '2026-09-14',
        ],
        [
            'mata_kuliah' => 'Pemrograman Berbasis Kerangka Kerja',
            'judul' => 'Secure Feedback Hub (Form GET/POST)',
            'deskripsi' => 'Portal umpan balik mahasiswa dengan validasi Form Request, proteksi CSRF, dan captcha penjumlahan.',
            'tautan' => null,
            'dikumpulkan_pada' => '2026-09-21',
        ],
        [
            'mata_kuliah' => 'Pemrograman Berbasis Kerangka Kerja',
            'judul' => 'Tugas 4: Aplikasi Multi-View Profil Akademik',
            'deskripsi' => 'Master layout Blade, komponen anonim, dan halaman Ide-Riset yang memvisualisasikan pipeline Agentic AI.',
            'tautan' => 'https://github.com/Pr4dh1pt4/PBKK-Agentic-AI',
            'dikumpulkan_pada' => '2026-09-24',
        ],
        [
            'mata_kuliah' => 'Pemrograman Berbasis Kerangka Kerja',
            'judul' => 'Secure Academic Portal Database',
            'deskripsi' => 'Memindahkan data portofolio dari variabel controller ke MySQL: migration, foreign key cascade, $fillable, factory, dan seeder.',
            'tautan' => null,
            'dikumpulkan_pada' => '2026-10-07',
        ],
    ];

    /**
     * Buat atau perbarui akun demo beserta project dan riwayat tugasnya.
     */
    public function run(): void
    {
        $dosen = User::updateOrCreate(
            ['email' => 'dosenpbkk@its.ac.id'],
            [
                'name' => 'Dosen PBKK',
                'password' => 'password', // Di-hash otomatis oleh cast 'hashed' di model User.
            ],
        );

        // email_verified_at tidak ada di $fillable, jadi diisi terpisah agar lolos middleware 'verified'.
        $dosen->forceFill(['email_verified_at' => $dosen->email_verified_at ?? now()])->save();

        foreach (self::PROJECTS as $data) {
            $tanggal = now()->subDays($data['hari_lalu']);
            unset($data['hari_lalu']);

            // Lewat relasi, user_id terisi otomatis tanpa perlu masuk $fillable.
            $project = $dosen->projects()->updateOrCreate(['judul' => $data['judul']], $data);
            $project->forceFill(['created_at' => $tanggal, 'updated_at' => $tanggal])->save();
        }

        foreach (self::ASSIGNMENTS as $data) {
            $dosen->assignments()->updateOrCreate(['judul' => $data['judul']], $data);
        }
    }
}
