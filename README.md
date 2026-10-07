# Secure Academic Portal Database

Tugas portofolio PBKK: memindahkan data portofolio akademik dari variabel controller (minggu lalu) ke database MySQL dengan Laravel 13 + Breeze.

**Pradhipta Raja Mahendra** · Teknik Informatika · Institut Teknologi Sepuluh Nopember (ITS)

## Akun Demo Penguji

| Email | Password |
|---|---|
| `dosenpbkk@its.ac.id` | `password` |

Akun ini berisi 3 proposal Agentic AI yang ditulis manual (termasuk platform Agentic AI otonom untuk pengujian aplikasi web yang sudah di-deploy) dan riwayat tugas PBKK. 15 akun mahasiswa acak (`...@student.its.ac.id`) juga memakai password `password`.

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

Buka http://127.0.0.1:8000, klik **Masuk**, dan login dengan akun demo.

## Skema Database

| Tabel | Kolom penting | Catatan |
|---|---|---|
| `users` | `name`, `email` (**unique**), `password` | Tabel bawaan Breeze. |
| `projects` | `user_id`, `judul`, `deskripsi` (**nullable**), `tema_agent` (**default `Ollama`**), `api_key_secure` | Proposal ide proyek Agentic AI. |
| `assignments` | `user_id`, `mata_kuliah`, `judul`, `deskripsi`, `tautan`, `nilai`, `dikumpulkan_pada` | Riwayat tugas kuliah. |

- `user_id` di `projects` dan `assignments` memakai `foreignId()->constrained('users')->cascadeOnDelete()`. Menghapus user ikut menghapus data miliknya.
- `api_key_secure` bertipe `TEXT`, bukan `VARCHAR(255)`, karena disimpan terenkripsi. Payload enkripsi untuk key 36 karakter sudah sepanjang 256 karakter.
- Alasan pemilihan tipe setiap kolom ditulis sebagai komentar di file migration.

## Keamanan

- **Mass assignment:** model `Project` dan `Assignment` mendefinisikan `$fillable` secara eksplisit **tanpa `user_id`**. Kepemilikan diisi lewat relasi, misalnya `$user->projects()->create([...])`, sehingga request tidak bisa menyisipkan `user_id` milik orang lain.
- **Enkripsi:** `api_key_secure` memakai cast `encrypted` (dienkripsi dengan `APP_KEY`) dan masuk `$hidden`, sehingga tidak ikut saat model diubah ke JSON. Di dashboard hanya tampil ter-mask, misalnya `sk-****0a6e`.
- **Query dashboard:** `$request->user()->projects()->latest()->get()` hanya mengambil data milik user yang login, diurutkan dari yang terbaru. Eloquent memakai prepared statement, tanpa raw query.
- **Route:** `/dashboard` dilindungi middleware `auth` dan `verified`.
- **XSS:** semua output Blade memakai `{{ }}`. Tautan tugas hanya dirender bila diawali `https://`.
- **`.env`:** tercantum di `.gitignore` dan tidak di-commit.

## Factory dan Seeder

- `UserFactory`: nama Indonesia (`id_ID`), email unik `nama.belakang.1234@student.its.ac.id`.
- `ProjectFactory`: judul dan deskripsi dirangkai dari 8 domain (pendidikan, kesehatan, logistik, keuangan, pertanian, layanan publik, software testing, keamanan siber) dengan tujuan konkret per domain, 4 jenis agen, dan 6 provider (Ollama, OpenAI, Claude, Gemini, LangChain, CrewAI). API key berupa string acak dummy.
- `AssignmentFactory`: tugas realistis dari beberapa mata kuliah Informatika.
- `DatabaseSeeder` memanggil:
  - `StudentSeeder`: 15 mahasiswa, masing-masing tepat 2 project dan 3 riwayat tugas.
  - `DemoUserSeeder`: akun demo via `updateOrCreate`.

`php artisan db:seed` aman dijalankan berulang kali. `StudentSeeder` hanya membuat kekurangan dari 15 mahasiswa, dan akun demo di-upsert. Setelah `migrate:fresh --seed`, hasilnya selalu **16 user, 33 project, 50 riwayat tugas**.

## Pengujian

```bash
php artisan test          # feature test Breeze + DashboardTest (isolasi data & cascade)
./vendor/bin/pint --test  # PSR-12
```

## Struktur File Utama

```
app/Http/Controllers/DashboardController.php
app/Models/{User,Project,Assignment}.php
database/migrations/*_create_projects_table.php
database/migrations/*_create_assignments_table.php
database/factories/{User,Project,Assignment}Factory.php
database/seeders/{Database,Student,DemoUser}Seeder.php
resources/views/dashboard.blade.php
tests/Feature/DashboardTest.php
```

Halaman portofolio publik dari tugas minggu lalu (`/`, `/profil-mahasiswa`, `/ide-agent`) tetap tersedia, memakai layout `resources/views/layouts/portfolio.blade.php`.
