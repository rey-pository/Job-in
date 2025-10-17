@extends('layouts.jobseeker')

@section('content')
<div x-data="jobApplication()">
    <div class="mb-6">
        <a href="{{ route('jobseeker.dashboard') }}" class="text-indigo-600 hover:text-indigo-800">&larr; Kembali ke Daftar Lowongan</a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0 mr-5">
                    @if($job->company && $job->company->logo)
                        <img class="h-16 w-16 object-cover rounded-lg" src="/storage/{{ $job->company->logo }}" alt="{{ $job->company->name }}">
                    @else
                        <div class="h-16 w-16 bg-gray-200 flex items-center justify-center rounded-lg text-gray-500 font-bold text-2xl">
                            {{ substr($job->company->name ?? 'C', 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="flex-grow">
                    <span class="text-xs font-semibold uppercase text-indigo-700 bg-indigo-100 px-2.5 py-0.5 rounded-full">{{ str_replace('-', ' ', $job->work_type) }}</span>
                    <h1 class="text-3xl font-bold mt-1">{{ $job->title }}</h1>
                    <p class="text-gray-600 text-lg">{{ $job->company->name ?? 'Nama Perusahaan' }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ collect([$job->city, $job->province])->filter()->join(', ') ?: 'Lokasi tidak tersedia' }}</p>
                </div>
            </div>
        </div>
        
        <div class="px-6 pb-6 border-t pt-4">
            <h2 class="text-xl font-semibold mb-3">Deskripsi Pekerjaan</h2>
            <div class="prose max-w-none text-gray-700">
                {!! nl2br(e($job->description)) !!}
            </div>
        </div>

        <div class="p-4 bg-gray-50 text-right">
            @if($hasApplied)
                <button class="bg-gray-400 text-white py-2 px-6 rounded-lg cursor-not-allowed" disabled>Anda Sudah Melamar</button>
            @else
                <button @click="isApplyOpen = true" class="bg-indigo-600 text-white py-2 px-6 rounded-lg hover:bg-indigo-700">Lamar Pekerjaan Ini</button>
            @endif
        </div>
    </div>

    <div x-show="isApplyOpen" x-transition class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4" @click.away="isApplyOpen = false" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg" @click.stop>
            <form @submit.prevent="submitApplication($event)">
                <div class="p-6 border-b">
                    <h2 class="text-2xl font-bold">Lamar Posisi: {{ $job->title }}</h2>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-red-500 text-sm" x-text="applyError"></p>
                    <div><label class="block mb-1 font-medium">Upload CV (PDF, max 2MB)</label><input type="file" name="cv" class="w-full text-sm" required></div>
                    <div><label class="block mb-1 font-medium">Upload KTP (JPG/PNG/PDF, max 2MB)</label><input type="file" name="ktp" class="w-full text-sm" required></div>
                </div>
                <div class="p-4 bg-gray-50 border-t flex justify-end space-x-3">
                    <button @click="isApplyOpen = false" type="button" class="bg-gray-200 py-2 px-4 rounded">Batal</button>
                    <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded" :disabled="isSubmitting" x-text="isSubmitting ? 'Mengirim...' : 'Kirim Lamaran'"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function jobApplication() {
        return {
            isApplyOpen: false,
            isSubmitting: false,
            applyError: '',

            async submitApplication(event) {
                this.isSubmitting = true;
                this.applyError = '';
                const form = event.target;
                const formData = new FormData(form);
                
                try {
                    const response = await fetch(`/api/jobs/{{ $job->id }}/apply`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                        credentials: 'include',
                        body: formData
                    });
                    
                    if (!response.ok) {
                        const errorData = await response.json();
                        let errorMessage = errorData.message || 'Gagal mengirim lamaran.';
                        if (errorData.errors) {
                            errorMessage = Object.values(errorData.errors).flat().join(' ');
                        }
                        throw new Error(errorMessage);
                    }

                    alert('Lamaran berhasil dikirim!');
                    this.isApplyOpen = false;
                    window.location.reload();

                } catch (error) {
                    this.applyError = error.message;
                } finally {
                    this.isSubmitting = false;
                }
            }
        }
    }
</script>
@endsection