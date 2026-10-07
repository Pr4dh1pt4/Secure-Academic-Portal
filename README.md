# Secure Academic Portal Database

Tugas portofolio PBKK: memindahkan data portofolio akademik dari variabel controller (minggu lalu) ke database MySQL dengan Laravel 13 + Breeze.

**Pradhipta Raja Mahendra** · Teknik Informatika · Institut Teknologi Sepuluh Nopember (ITS)

## Akun

| Peran | Email | Password |
|---|---|---|
| Dosen penguji (demo) | `dosenpbkk@its.ac.id` | `password` |
| Pemilik portofolio (mahasiswa) | `5025241055@student.its.ac.id` | `password` |
| 14 mahasiswa acak | `NRP@student.its.ac.id` (lihat tabel `users`) | `password` |

- **Akun dosen** berisi 3 proposal Agentic AI yang ditulis manual, termasuk platform Agentic AI otonom untuk pengujian aplikasi web yang sudah di-deploy.
- **Akun pemilik portofolio** berisi profil diri, 2 proposal (proposal unggulan AutoQA Agent lengkap dengan tahapan alur kerja), dan 5 riwayat tugas PBKK. Data akun inilah yang ditampilkan di halaman portofolio publik.
- Setiap mahasiswa punya NRP, program studi, bio, minat, dan keahlian sendiri.

## Menjalankan Project

Prasyarat: PHP 8.3+, Composer, Node.js, MySQL/MariaDB.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database di `.env` (`DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`), lalu buat database `secure_academic_portal`:

```sql
CREATE DATABASE secure_academic_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```bash
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

Buka http://127.0.0.1:8000 untuk portofolio publik, atau klik **Masuk** dan login dengan salah satu akun di atas.

## Skema Database

| Tabel | Kolom penting | Catatan |
|---|---|---|
| `users` | `name`, `email` (**unique**), `password`, `nrp` (unique, nullable), `program_studi`, `bio`, `minat` (JSON), `keahlian` (JSON) | Tabel bawaan Breeze, ditambah kolom profil diri lewat migration terpisah. |
| `projects` | `user_id`, `judul`, `deskripsi` (**nullable**), `tahapan` (JSON), `tema_agent` (**default `Ollama`**), `api_key_secure` | Proposal ide proyek Agentic AI. |
| `assignments` | `user_id`, `mata_kuliah`, `judul`, `deskripsi`, `tautan`, `nilai`, `dikumpulkan_pada` | Riwayat tugas kuliah. |

- `user_id` di `projects` dan `assignments` memakai `foreignId()->constrained('users')->cascadeOnDelete()`. Menghapus user ikut menghapus data miliknya.
- `api_key_secure` bertipe `TEXT`, bukan `VARCHAR(255)`, karena disimpan terenkripsi. Payload enkripsi untuk key 36 karakter sudah sepanjang 256 karakter.
- Alasan pemilihan tipe setiap kolom ditulis sebagai komentar di file migration.

## Dari Data Statis ke Database

Di Tugas 4, profil, skill, dan tahapan pipeline AutoQA Agent ditulis sebagai array di `PageController`. Sekarang semuanya dibaca dari database:

| Halaman | Sumber data |
|---|---|
| `/` Beranda | Nama, bio, jumlah proposal, dan jumlah tugas pemilik portofolio |
| `/profil-mahasiswa` | Kolom profil di `users` + riwayat tugas dari `assignments` |
| `/ide-agent` | Proposal unggulan (punya `tahapan`) dan proposal lain dari `projects`. Formulir ide menyimpan ke `projects` milik user yang login. |
| `/dashboard` | Data milik user yang sedang login |

Akun pemilik portofolio diatur lewat `PORTFOLIO_OWNER_EMAIL` (default di `config/portfolio.php`).

## Keamanan

- **Mass assignment:** model `Project` dan `Assignment` mendefinisikan `$fillable` secara eksplisit **tanpa `user_id`**. Kepemilikan diisi lewat relasi, misalnya `$user->projects()->create([...])`, sehingga request tidak bisa menyisipkan `user_id` milik orang lain.
- **Enkripsi:** `api_key_secure` memakai cast `encrypted` (dienkripsi dengan `APP_KEY`) dan masuk `$hidden`, sehingga tidak ikut saat model diubah ke JSON. Di dashboard hanya tampil ter-mask, misalnya `sk-****0a6e`.
- **Query dashboard:** `$request->user()->projects()->latest()->get()` hanya mengambil data milik user yang login, diurutkan dari yang terbaru. Eloquent memakai prepared statement, tanpa raw query.
- **Route:** `/dashboard` dan `POST /ide-agent` dilindungi middleware `auth` dan `verified`.
- **Formulir ide:** divalidasi `StoreProjectRequest` (`tema_agent` harus salah satu pilihan), lalu disimpan lewat `$request->user()->projects()->create($request->validated())`. `user_id` yang disisipkan ke request diabaikan.
- **XSS:** semua output Blade memakai `{{ }}`. Tautan tugas hanya dirender bila diawali `https://`.
- **`.env`:** tercantum di `.gitignore` dan tidak di-commit.

## Factory dan Seeder

- `UserFactory`: nama Indonesia (`id_ID`), NRP sesuai kode prodi (5024–5027), email unik `NRP@student.its.ac.id`, serta bio, minat, dan keahlian acak.
- `ProjectFactory`: judul dan deskripsi dirangkai dari 8 domain (pendidikan, kesehatan, logistik, keuangan, pertanian, layanan publik, software testing, keamanan siber) dengan tujuan konkret per domain, 4 jenis agen, dan 6 provider (Ollama, OpenAI, Claude, Gemini, LangChain, CrewAI). API key berupa string acak dummy.
- `AssignmentFactory`: tugas realistis dari beberapa mata kuliah Informatika.
- `DatabaseSeeder` memanggil:
  - `PortfolioOwnerSeeder`: akun pemilik portofolio via `updateOrCreate`.
  - `StudentSeeder`: mahasiswa acak hingga total 15 mahasiswa (pemilik + 14 acak), masing-masing tepat 2 project dan 3 riwayat tugas.
  - `DemoUserSeeder`: akun dosen demo via `updateOrCreate`.

`php artisan db:seed` aman dijalankan berulang kali. `StudentSeeder` hanya membuat kekurangan dari 15 mahasiswa, sedangkan akun pemilik dan akun demo di-upsert. Setelah `migrate:fresh --seed`, hasilnya selalu **16 user (15 mahasiswa + 1 dosen), 33 project (15×2 + 3), 47 riwayat tugas (14×3 + 5)**.

## Pengujian

```bash
php artisan test          # Breeze + DashboardTest (isolasi data, cascade) + PortfolioTest (halaman publik dari DB, formulir aman)
./vendor/bin/pint --test  # PSR-12
```

## Struktur File Utama

```
app/Http/Controllers/{Dashboard,Page}Controller.php
app/Http/Requests/StoreProjectRequest.php
config/portfolio.php
app/Models/{User,Project,Assignment}.php
database/migrations/*_create_projects_table.php
database/migrations/*_create_assignments_table.php
database/migrations/*_add_profile_columns_to_users_table.php
database/migrations/*_add_tahapan_to_projects_table.php
database/factories/{User,Project,Assignment}Factory.php
database/seeders/{Database,PortfolioOwner,Student,DemoUser}Seeder.php
resources/views/dashboard.blade.php
resources/views/pages/{beranda,profil,ide-agent}.blade.php
tests/Feature/{Dashboard,Portfolio}Test.php
```

Halaman portofolio publik memakai layout `resources/views/layouts/portfolio.blade.php`; halaman yang butuh login memakai layout Breeze.
