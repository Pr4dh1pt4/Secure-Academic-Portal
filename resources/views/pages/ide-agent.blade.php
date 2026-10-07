@extends('layouts.portfolio')

@section('title', 'Ide-Riset Agentic AI')

@php
    // Tantangan 1: class Tailwind dipilih dari variabel Blade $isDark (?mode=dark)
    $card = $isDark ? 'border-slate-700 bg-slate-900 text-slate-100' : 'border-slate-200 bg-white text-slate-800';
    $muted = $isDark ? 'text-slate-400' : 'text-slate-600';
    $heading = $isDark ? 'text-white' : 'text-slate-900';
    $input = $isDark
        ? 'border-slate-700 bg-slate-800 text-slate-100 placeholder-slate-500'
        : 'border-slate-300 bg-white text-slate-900 placeholder-slate-400';
@endphp

@section('content')
    <div class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-teal-500">Rancangan Platform Agentic AI Kelompok</p>
            <h1 class="text-3xl font-bold sm:text-4xl {{ $heading }}">AutoQA Agent</h1>
        </div>
        <a href="{{ route('ide-agent', $isDark ? [] : ['mode' => 'dark']) }}"
           class="rounded-lg border px-4 py-2 text-sm font-medium transition {{ $isDark ? 'border-slate-600 hover:bg-slate-800' : 'border-slate-300 hover:bg-slate-100' }}">
            {{ $isDark ? '☀ Mode Terang' : '☾ Mode Gelap' }}
        </a>
    </div>

    <p class="mb-10 max-w-4xl leading-relaxed {{ $muted }}">
        AutoQA Agent adalah autonomous web agent berbasis AI yang menguji aplikasi web yang sudah dideploy, mendeteksi isu
        fungsional, aksesibilitas, performa, dan keamanan, lalu menganalisis source code terkait dari repository Git yang
        terhubung. Agent kemudian menghasilkan perbaikan (fix), memvalidasinya lewat automated testing, dan membuat
        verified pull request untuk direview manusia.
    </p>

    {{-- Diagram pipeline: vertikal di layar kecil, horizontal di layar besar --}}
    <section aria-label="Pipeline AutoQA Agent" class="mb-12">
        <h2 class="mb-4 text-xl font-semibold {{ $heading }}">Alur Kerja Agent</h2>
        <ol class="flex flex-col items-stretch lg:flex-row">
            @foreach ($stages as $stage)
                <li class="flex flex-col items-center lg:flex-1 lg:flex-row">
                    <div class="w-full rounded-xl border p-4 shadow-sm lg:h-full {{ $card }}">
                        <span class="mb-3 flex size-9 items-center justify-center rounded-full bg-teal-600 text-sm font-bold text-white">
                            {{ $loop->iteration }}
                        </span>
                        <h3 class="mb-1 text-sm font-semibold leading-snug">{{ $stage['title'] }}</h3>
                        <p class="text-xs leading-relaxed {{ $muted }}">{{ $stage['desc'] }}</p>
                    </div>
                    @unless ($loop->last)
                        <span aria-hidden="true" class="py-1 text-2xl text-teal-500 lg:px-1 lg:py-0">
                            <span class="lg:hidden">↓</span><span class="hidden lg:inline">→</span>
                        </span>
                    @endunless
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Formulir pengumpulan ide --}}
    <section class="max-w-2xl rounded-2xl border p-6 shadow-sm {{ $card }}">
        <h2 class="mb-1 text-xl font-semibold {{ $heading }}">Formulir Pengumpulan Ide</h2>
        <p class="mb-5 text-sm {{ $muted }}">Punya ide pengembangan AutoQA Agent? Kirimkan di sini.</p>

        @if (session('status'))
            <x-status-banner type="success" title="Berhasil" :dark="$isDark" class="mb-5">
                {{ session('status') }}
            </x-status-banner>
        @endif

        @if ($errors->any())
            <x-status-banner type="error" title="Periksa kembali isian formulir" :dark="$isDark" class="mb-5">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-status-banner>
        @endif

        <form method="POST" action="{{ route('ide-agent.submit') }}" class="space-y-4">
            @csrf
            @if ($isDark)
                <input type="hidden" name="mode" value="dark">
            @endif

            <div>
                <label for="judul" class="mb-1 block text-sm font-medium">Judul Ide</label>
                <input id="judul" name="judul" type="text" value="{{ old('judul') }}" required maxlength="120"
                       placeholder="Contoh: Deteksi regresi visual otomatis"
                       class="w-full rounded-lg border px-3 py-2 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/30 focus:outline-none {{ $input }}">
            </div>

            <div>
                <label for="deskripsi" class="mb-1 block text-sm font-medium">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" required maxlength="1000"
                          placeholder="Jelaskan ide secara singkat..."
                          class="w-full rounded-lg border px-3 py-2 text-sm focus:border-teal-500 focus:ring-2 focus:ring-teal-500/30 focus:outline-none {{ $input }}">{{ old('deskripsi') }}</textarea>
            </div>

            <button type="submit" class="rounded-lg bg-teal-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-teal-700">
                Kirim Ide
            </button>
        </form>
    </section>
@endsection
