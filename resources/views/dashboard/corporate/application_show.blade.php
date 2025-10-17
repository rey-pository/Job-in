@extends('layouts.corporate')

@section('content')
    <div class="mb-6">
        <a href="{{ route('corporate.applications') }}" class="text-indigo-600 hover:text-indigo-800">&larr; Kembali ke Daftar Pelamar</a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h1 class="text-3xl font-bold text-gray-800">{{ $application->user->name ?? $application->name }}</h1>
            <p class="text-gray-600">Melamar untuk posisi: <span class="font-semibold">{{ $application->job->title ?? 'N/A' }}</span></p>
            <div class="mt-4 flex space-x-6">
                <a href="/storage/{{ $application->cv_path }}" target="_blank" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Download CV</a>
                <a href="/storage/{{ $application->ktp_path }}" target="_blank" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">Download KTP</a>
            </div>
        </div>

        <div class="px-6 pb-6 border-t pt-4">
            <h2 class="text-xl font-semibold mb-3">Riwayat Pendidikan</h2>
            @forelse($application->user->educationHistories as $edu)
                <div class="border-b py-2"><p class="font-bold">{{ $edu->degree }}</p><p>{{ $edu->institution }} ({{ $edu->start_year }} - {{ $edu->end_year ?? 'Sekarang' }})</p></div>
            @empty
                <p class="text-gray-500">Tidak ada data.</p>
            @endforelse
        </div>
        
        <div class="px-6 pb-6 pt-4">
            <h2 class="text-xl font-semibold mb-3">Pengalaman Kerja</h2>
            @forelse($application->user->workExperiences as $work)
                <div class="border-b py-2"><p class="font-bold">{{ $work->position }}</p><p>{{ $work->company_name }} ({{ $work->start_date }} - {{ $work->end_date ?? 'Sekarang' }})</p></div>
            @empty
                <p class="text-gray-500">Tidak ada data.</p>
            @endforelse
        </div>
        
        <div class="px-6 pb-6 pt-4">
            <h2 class="text-xl font-semibold mb-3">Pengalaman Organisasi</h2>
            @forelse($application->user->organizationExperiences as $org)
                <div class="border-b py-2"><p class="font-bold">{{ $org->role }}</p><p>{{ $org->organization_name }} ({{ $org->start_year }} - {{ $org->end_year ?? 'Sekarang' }})</p></div>
            @empty
                <p class="text-gray-500">Tidak ada data.</p>
            @endforelse
        </div>

        <div class="px-6 pb-6 pt-4">
            <h2 class="text-xl font-semibold mb-3">Portofolio</h2>
            @forelse($application->user->portfolioHistories as $portfolio)
                <div class="border-b py-2"><p class="font-bold">{{ $portfolio->title }}</p><p>{{ $portfolio->type }} - {{ $portfolio->issuer ?? '' }} ({{ $portfolio->date }})</p></div>
            @empty
                <p class="text-gray-500">Tidak ada data.</p>
            @endforelse
        </div>
    </div>
@endsection