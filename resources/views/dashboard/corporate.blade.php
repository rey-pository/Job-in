@extends('layouts.corporate')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Beranda</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm font-medium">Kunjungan Profil</h3>
            <p class="text-3xl font-bold">{{ number_format($profileVisits) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-gray-500 text-sm font-medium">Lowongan Aktif</h3>
            <p class="text-3xl font-bold">{{ $activeJobsCount }}</p>
        </div>
    </div>
@endsection