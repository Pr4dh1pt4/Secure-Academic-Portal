<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Beranda. Tantangan 2: query ?user=Nama → sambutan dinamis.
     */
    public function beranda(Request $request): View
    {
        $user = trim((string) $request->query('user', ''));

        return view('pages.beranda', [
            'user' => $user !== '' ? $user : null,
        ]);
    }

    /**
     * Profil mahasiswa.
     */
    public function profil(): View
    {
        return view('pages.profil', [
            'profile' => [
                'name' => 'Pradhipta Raja Mahendra',
                'campus' => 'Institut Teknologi Sepuluh Nopember (ITS), Surabaya',
                'major' => 'Teknik Informatika',
                'interests' => ['Data Engineering', 'Competitive Programming', 'Software Engineering'],
            ],
            'skills' => ['PHP & Laravel', 'Python', 'C++', 'SQL', 'Git', 'Tailwind CSS'],
        ]);
    }

    /**
     * Ide-Riset: rancangan platform Agentic AI + formulir ide.
     * Tantangan 1: query ?mode=dark → tema gelap.
     */
    public function ideAgent(Request $request): View
    {
        return view('pages.ide-agent', [
            'isDark' => $request->query('mode') === 'dark',
            'stages' => [
                ['title' => 'Web App Under Test', 'desc' => 'Agent mengakses aplikasi yang sudah live/deployed.'],
                ['title' => 'Testing & Issue Detection', 'desc' => 'Scan fungsional, aksesibilitas, performa, dan keamanan.'],
                ['title' => 'Source Code Analysis', 'desc' => 'Menelusuri kode terkait di repository Git yang terhubung.'],
                ['title' => 'Fix Generation', 'desc' => 'Menyusun perbaikan kode untuk isu yang ditemukan.'],
                ['title' => 'Automated Validation', 'desc' => 'Fix diuji ulang agar tidak merusak fungsi lain.'],
                ['title' => 'Verified Pull Request', 'desc' => 'PR berisi fix tervalidasi, siap direview manusia.'],
            ],
        ]);
    }

    /**
     * Terima formulir ide. Belum disimpan ke database, cukup redirect dengan pesan sukses.
     */
    public function submitIde(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:120'],
            'deskripsi' => ['required', 'string', 'max:1000'],
        ], [
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
        ], [
            'judul' => 'Judul ide',
            'deskripsi' => 'Deskripsi',
        ]);

        $query = $request->input('mode') === 'dark' ? ['mode' => 'dark'] : [];

        return redirect()
            ->route('ide-agent', $query)
            ->with('status', 'Ide "'.$validated['judul'].'" berhasil dikirim. Terima kasih!');
    }
}
