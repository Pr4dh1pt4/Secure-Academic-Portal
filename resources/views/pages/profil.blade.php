@extends('layouts.portfolio')

@section('title', 'Profil Mahasiswa')

@section('content')
    <h1 class="mb-6 text-3xl font-bold text-slate-900">Profil Mahasiswa</h1>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-info-card
            class="lg:col-span-2"
            :name="$profile['name']"
            :campus="$profile['campus']"
            :major="$profile['major']"
            :interests="$profile['interests']"
        />

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-900">Skill</h2>
            <ul class="space-y-2 text-sm">
                @foreach ($skills as $skill)
                    <li class="flex items-center gap-2">
                        <span class="size-2 rounded-full bg-teal-500"></span>
                        {{ $skill }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
