<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Dashboard profil: data diri, proposal project, dan riwayat tugas milik user yang sedang login.
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard.
     *
     * Query diambil lewat relasi user yang login, sehingga hanya data miliknya
     * yang muncul. Eloquent memakai prepared statement, jadi aman dari SQL injection.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'user' => $user,
            'peran' => Str::endsWith($user->email, '@student.its.ac.id') ? 'Mahasiswa' : 'Dosen / Penguji',
            'projects' => $user->projects()->latest()->get(),
            'assignments' => $user->assignments()->latest('dikumpulkan_pada')->get(),
        ]);
    }
}
