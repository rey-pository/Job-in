@extends('layouts.jobseeker')

@section('content')
    <h1 class="text-3xl font-bold mb-4">Lowongan Kerja Terbaru</h1>

    <form id="filterForm" method="GET" action="{{ route('jobseeker.dashboard') }}" class="bg-white p-4 rounded-lg shadow-sm mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div class="md-col-span-4">
                <label for="search" class="block text-sm font-medium text-gray-700">Cari Lowongan</label>
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" id="searchInput" class="w-full pl-10 p-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., Back End Developer" value="{{ request('search') }}">
                </div>
            </div>
        </div>
    </form>

    <div id="jobsList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($jobs as $job)
            <div class="bg-white p-4 rounded-lg shadow-md flex flex-col justify-between hover:shadow-lg transition-shadow duration-300">
                <div>
                    <div class="flex items-start justify-between">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-3">
                                @if($job->company && $job->company->logo)
                                    <img class="h-10 w-10 object-cover rounded-md" src="/storage/{{ $job->company->logo }}" alt="{{ $job->company->name }}">
                                @else
                                    <div class="h-10 w-10 bg-gray-200 flex items-center justify-center rounded-md text-gray-500 font-bold">
                                        {{ substr($job->company->name ?? 'C', 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow">
                                <h4 class="text-base font-bold text-gray-800 leading-tight">{{ $job->title }}</h4>
                                <p class="mt-1 text-sm text-gray-500">{{ $job->company->name ?? 'Nama Perusahaan' }}</p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold uppercase text-indigo-700 bg-indigo-100 px-2.5 py-0.5 rounded-full whitespace-nowrap">{{ str_replace('-', ' ', $job->work_type) }}</span>
                    </div>
                    <div class="mt-3 flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>{{ collect([$job->city, $job->province])->filter()->join(', ') ?: 'Lokasi tidak tersedia' }}</span>
                    </div>
                </div>
                <div class="mt-4 text-right">
                    {{-- INI PERBAIKANNYA --}}
                    <a href="{{ route('jobseeker.jobs.show', $job->id) }}" class="text-sm text-indigo-600 font-semibold hover:text-indigo-800">Lihat Detail &rarr;</a>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 lg:col-span-3 text-center py-10 bg-white rounded-lg shadow-md">
                <p class="text-gray-500 text-lg">Tidak ada lowongan yang cocok dengan kriteria Anda.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $jobs->links() }}
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('filterForm');
        const searchInput = document.getElementById('searchInput');
        let debounceTimer;

        function submitForm() {
            form.submit();
        }

        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(submitForm, 500);
        });
    });
</script>
@endsection