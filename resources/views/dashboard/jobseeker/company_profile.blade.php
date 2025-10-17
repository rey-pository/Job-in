@extends('layouts.jobseeker')

@section('content')
    <div class="mb-6">
        <a href="{{ route('jobseeker.companies') }}" class="text-indigo-600 hover:text-indigo-800">&larr; Kembali ke Daftar Perusahaan</a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0 mr-6">
                    @if($company->logo)
                        <img class="h-24 w-24 object-cover rounded-lg border p-1" src="/storage/{{ $company->logo }}" alt="{{ $company->name }}">
                    @else
                        <div class="h-24 w-24 bg-gray-200 flex items-center justify-center rounded-lg text-gray-500 font-bold text-4xl">
                            {{ substr($company->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="flex-grow">
                    <h1 class="text-3xl font-bold text-gray-800">{{ $company->name }}</h1>
                    @if($company->website)
                        <a href="{{ $company->website }}" target="_blank" class="text-indigo-600 hover:underline mt-1 block">{{ $company->website }}</a>
                    @endif
                    <p class="mt-2 text-sm text-gray-500">{{ $company->address ?: 'Alamat tidak tersedia' }}</p>
                </div>
            </div>
        </div>
        
        <div class="px-6 pb-6 border-t pt-4">
            <h2 class="text-xl font-semibold mb-3">Tentang Perusahaan</h2>
            <div class="prose max-w-none text-gray-700">
                <p>{{ $company->description ?: 'Deskripsi tidak tersedia.' }}</p>
            </div>
        </div>
    </div>

    <div class="mt-8">
        <h2 class="text-2xl font-bold mb-4">Lowongan Tersedia</h2>
        <div class="space-y-4">
            @forelse($jobs as $job)
                <a href="{{ route('jobseeker.jobs.show', $job->id) }}" class="block bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-lg text-gray-800">{{ $job->title }}</h3>
                            <p class="text-sm text-gray-500">{{ collect([$job->city, $job->province])->filter()->join(', ') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold uppercase text-indigo-700 bg-indigo-100 px-2.5 py-0.5 rounded-full">{{ str_replace('-', ' ', $job->work_type) }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white p-6 rounded-lg shadow-md text-center">
                    <p class="text-gray-500">Saat ini tidak ada lowongan tersedia dari perusahaan ini.</p>
                </div>
            @endforelse

            <div class="mt-6">
                {{ $jobs->links() }}
            </div>
        </div>
    </div>
@endsection