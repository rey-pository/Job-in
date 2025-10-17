@extends('layouts.jobseeker')

@section('content')
    <h1 class="text-3xl font-bold mb-4">Daftar Perusahaan</h1>
    <p class="text-gray-600 mb-6">Jelajahi perusahaan-perusahaan yang telah bergabung dengan Job-In.</p>

    <form method="GET" action="{{ route('jobseeker.companies') }}" class="bg-white p-4 rounded-lg shadow-sm mb-6">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="search" class="w-full pl-10 p-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="Cari nama perusahaan..." value="{{ request('search') }}">
        </div>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($companies as $company)
            <div class="bg-white p-5 rounded-lg shadow-md flex flex-col items-center text-center">
                <div class="flex-shrink-0 mb-4">
                    @if($company->logo)
                        <img class="h-20 w-20 object-contain rounded-full border p-1" src="/storage/{{ $company->logo }}" alt="{{ $company->name }}">
                    @else
                        <div class="h-20 w-20 bg-gray-200 flex items-center justify-center rounded-full text-gray-500 font-bold text-3xl">
                            {{ substr($company->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="flex-grow">
                    <h4 class="text-lg font-bold text-gray-800 leading-tight">{{ $company->name }}</h4>
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>{{ Str::limit($company->address, 25) ?: 'Lokasi tidak tersedia' }}</span>
                    </div>
                </div>
                <div class="mt-5 w-full">
                    <a href="{{ route('jobseeker.companies.show', $company->id) }}" class="w-full block bg-indigo-100 text-indigo-700 text-sm font-semibold py-2 px-4 rounded-lg hover:bg-indigo-200">
                        Lihat Selengkapnya
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-1 md:col-span-2 lg:col-span-4 text-center py-10 bg-white rounded-lg shadow-md">
                <p class="text-gray-500 text-lg">Tidak ada perusahaan yang cocok dengan pencarian Anda.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $companies->links() }}
    </div>
@endsection