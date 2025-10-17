<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Job-In: Temukan Karir Impianmu</title>
    @vite('resources/css/app.css')
    <style>
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased">

    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="/" class="text-2xl font-bold text-indigo-600">Job-In</a>
            <div class="space-x-4">
                <a href="{{ route('login.form') }}" class="text-gray-600 hover:text-indigo-600">Masuk</a>
                <a href="{{ route('login.form') }}" class="bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700">Daftar</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="bg-white">
            <div class="container mx-auto px-6 py-24 text-center">
                <h1 class="text-4xl md:text-6xl font-extrabold text-gray-800 leading-tight">
                    Temukan Karir Impianmu <span class="text-indigo-600">Berikutnya</span>.
                </h1>
                <p class="mt-4 text-lg text-gray-500 max-w-2xl mx-auto">
                    Job-In menghubungkan talenta terbaik dengan perusahaan terdepan di Indonesia untuk membangun masa depan karir yang lebih cerah.
                </p>
                <a href="#jobs" class="mt-8 inline-block bg-indigo-600 text-white py-3 px-8 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition duration-300">
                    Lihat Lowongan
                </a>
            </div>
        </section>

        <section class="py-16">
            <div class="container mx-auto px-6">
                <h3 class="text-center text-sm font-bold uppercase text-gray-400 tracking-widest">
                    Dipercaya oleh Perusahaan Terdepan
                </h3>
                <div class="mt-8 flex justify-center items-center flex-wrap gap-x-12 gap-y-6">
                    @forelse($companies as $company)
                        <a href="{{ route('login.form') }}" class="block">
                             <img class="h-8 filter grayscale hover:grayscale-0 transition duration-300" src="/storage/{{ $company->logo }}" alt="{{ $company->name }}">
                        </a>
                    @empty
                        <p class="text-gray-500">Belum ada perusahaan yang bergabung.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section id="jobs" class="py-20 bg-gray-100">
            <div class="container mx-auto px-6">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Lowongan Terbaru</h2>
                    <p class="mt-2 text-gray-500">Jelajahi beberapa peluang karir yang sedang menanti Anda.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse($jobs as $job)
                        <a href="{{ route('login.form') }}" class="block bg-white p-6 rounded-lg shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-4">
                                    @if($job->company && $job->company->logo)
                                        <img class="h-12 w-12 object-cover rounded-md" src="/storage/{{ $job->company->logo }}" alt="{{ $job->company->name }}">
                                    @else
                                        <div class="h-12 w-12 bg-gray-200 flex items-center justify-center rounded-md text-gray-500 font-bold">
                                            {{ substr($job->company->name ?? 'C', 0, 1) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-grow">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-xl font-bold text-gray-800">{{ $job->title }}</h4>
                                            <p class="mt-1 text-gray-500">{{ $job->company->name ?? 'Nama Perusahaan' }}</p>
                                        </div>
                                        <span class="text-xs font-semibold uppercase text-indigo-600 bg-indigo-100 px-2 py-1 rounded-full">{{ $job->work_type }}</span>
                                    </div>
                                    <div class="mt-4 flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span>{{ $job->city ?? 'Lokasi' }}, {{ $job->province }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 text-right">
                                <span class="text-indigo-600 font-semibold">Lamar Sekarang &rarr;</span>
                            </div>
                        </a>
                    @empty
                        <div class="md:col-span-2 lg:col-span-3 text-center py-10">
                            <p class="text-gray-500">Saat ini belum ada lowongan yang tersedia. Silakan cek kembali nanti!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="bg-indigo-700">
            <div class="container mx-auto px-6 py-20 text-center">
                 <h2 class="text-3xl md:text-4xl font-bold text-white">Jangkau Ribuan Talenta Profesional.</h2>
                 <p class="mt-4 text-indigo-200 max-w-2xl mx-auto">Daftarkan perusahaan Anda di Job-In dan mulailah mempublikasikan lowongan untuk menjangkau kandidat berkualitas di seluruh Indonesia.</p>
                 <a href="{{ route('login.form') }}" class="mt-8 inline-block bg-white text-indigo-600 py-3 px-8 rounded-lg text-lg font-semibold hover:bg-gray-100 transition duration-300">
                    Daftarkan Perusahaan
                </a>
            </div>
        </section>
        
    </main>

    <footer class="bg-gray-800 text-white">
        <div class="container mx-auto px-6 py-8 text-center">
            <p>&copy; {{ date('Y') }} Job-In. All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>