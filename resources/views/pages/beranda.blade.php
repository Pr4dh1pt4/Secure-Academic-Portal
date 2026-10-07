@extends('layouts.portfolio')

@section('title', 'Beranda')

@section('content')
    <x-status-banner type="{{ $user ? 'success' : 'info' }}" title="{{ $user ? 'Selamat datang, '.$user.'!' : 'Selamat datang!' }}" class="mb-8">
        @if ($user)
            Senang melihatmu di mini-website akademik saya.
        @else
            Coba tambahkan <code class="font-mono">?user=NamaKamu</code> di URL untuk sambutan personal.
        @endif
    </x-status-banner>

    <section class="mb-10">
        <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-teal-600">Tugas 4 · PBKK</p>
        <h1 class="mb-4 text-3xl font-bold text-slate-900 sm:text-4xl">Aplikasi Multi-View Profil Akademik</h1>
        <p class="max-w-3xl text-lg leading-relaxed text-slate-600">
            Mini-website akademik pribadi yang dibangun dengan Laravel, Blade, dan Tailwind CSS yang dikompilasi lewat Vite.
            Semua halaman memakai satu master layout dan komponen Blade yang bisa dipakai ulang.
        </p>
    </section>

    <section class="grid gap-4 sm:grid-cols-3">
        @foreach ([
            ['route' => 'profil', 'title' => 'Profil Mahasiswa', 'desc' => 'Data diri, kampus, jurusan, dan minat.'],
            ['route' => 'ide-agent', 'title' => 'Ide-Riset Agentic AI', 'desc' => 'Rancangan pipeline platform AutoQA Agent.'],
            ['route' => 'ide-agent', 'title' => 'Formulir Ide', 'desc' => 'Kirim ide riset baru melalui formulir.'],
        ] as $item)
            <a href="{{ route($item['route']) }}" class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-teal-400 hover:shadow-md">
                <h2 class="mb-1 font-semibold text-slate-900 group-hover:text-teal-700">{{ $item['title'] }} →</h2>
                <p class="text-sm text-slate-600">{{ $item['desc'] }}</p>
            </a>
        @endforeach
    </section>
@endsection
